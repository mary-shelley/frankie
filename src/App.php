<?php
namespace Corley\Middleware;

use ReflectionClass;
use ReflectionMethod;
use DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Corley\Middleware\Reader\HookReader;

class App
{
    private $container;
    private $router;
    private $request;
    private $response;
    private $reader;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getReader()
    {
        return $this->reader;
    }

    public function setReader(HookReader $reader)
    {
        $this->reader = $reader;
        return $this;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function setRouter(UrlMatcher $router)
    {
        $this->router = $router;

        return $this;
    }

    public function run(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        try {
            $matched = $this->getRouter()->matchRequest($request);

            $action     = $matched["action"];
            $controller = $matched["controller"];
            $this->executeBeforeActions($controller, $action);
            $controller = $this->getContainer()->get($controller);
            $actionReturn = call_user_func_array([$controller, $action], [$request, $response]);
            $this->executeAfterActions($controller, $action, $actionReturn);
        } catch (ResourceNotFoundException $e) {
            $response->setStatusCode(404);
        }

        return $response;
    }

    private function executeBeforeActions($controller, $action)
    {
        $this->executeBeforeSteps($this->getReader()->getBeforeMethodAnnotations($controller, $action));
        $this->executeBeforeSteps($this->getReader()->getBeforeClassAnnotations($controller));
    }

    private function executeBeforeSteps(array $annotations)
    {
        foreach ($annotations as $annotation) {
            $this->executeBeforeActions($annotation->targetClass, $annotation->targetMethod);
            $newController = $this->getContainer()->get($annotation->targetClass);
            call_user_func_array([$newController, $annotation->targetMethod], [
                $this->request, $this->response
            ]);
        }
    }

    private function executeAfterActions($controller, $action, $data)
    {
        $this->executeAfterSteps($this->getReader()->getAfterMethodAnnotations($controller, $action), $data);
        $this->executeAfterSteps($this->getReader()->getAfterClassAnnotations($controller), $data);
    }

    private function executeAfterSteps(array $annotations, $data)
    {
        foreach ($annotations as $annotation) {
            $this->executeAfterActions($annotation->targetClass, $annotation->targetMethod, $data);
            $newController = $this->getContainer()->get($annotation->targetClass);
            call_user_func_array([$newController, $annotation->targetMethod], [
                $this->request, $this->response, $data
            ]);
        }
    }
}
