<?php
namespace Corley\Middleware\Executor;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Corley\Middleware\Reader\HookReader;
use Corley\Middleware\Annotations\Before;
use Corley\Middleware\Annotations\After;
use Interop\Container\ContainerInterface;

class AnnotExecutor
{
    private $container;
    private $reader;
    private $request;
    private $response;

    public function __construct(ContainerInterface $container, HookReader $reader)
    {
        $this->container = $container;
        $this->reader = $reader;
    }

    public function execute(Request $request, Response $response, array $matched)
    {
        $this->request = $request;
        $this->response = $response;

        $action     = $matched["action"];
        $controller = $matched["controller"];

        $this->executeActionsFor($controller, $action, Before::class, $matched);
        $controller = $this->getContainer()->get($controller);
        $data = array_diff_key($matched, array_flip(["annotation", "_route", "controller", "action"]));
        $actionReturn = call_user_func_array([$controller, $action], array_merge([$request, $response], $data));
        $this->executeActionsFor($controller, $action, After::class, $actionReturn);
    }

    private function executeActionsFor($controller, $action, $filterClass, $data = null)
    {
        $methodAnnotations = $this->getReader()->getMethodAnnotationsFor($controller, $action, $filterClass);
        $this->executeSteps($methodAnnotations, [$this, __FUNCTION__], $filterClass, $data);

        $classAnnotations = $this->getReader()->getClassAnnotationsFor($controller, $filterClass);
        $this->executeSteps($classAnnotations, [$this, __FUNCTION__], $filterClass, $data);
    }

    private function executeSteps(array $annotations, callable $method, $filterClass, $data = null)
    {
        foreach ($annotations as $annotation) {
            $method($annotation->targetClass, $annotation->targetMethod, $filterClass, $data);
            $newController = $this->getContainer()->get($annotation->targetClass);
            call_user_func_array([$newController, $annotation->targetMethod], [
                $this->request, $this->response, $data
            ]);
        }
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getReader()
    {
        return $this->reader;
    }
}
