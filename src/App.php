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
use Corley\Middleware\Annotations\Before;
use Corley\Middleware\Annotations\After;

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

    public function run(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        try {
            $matched = $this->getRouter()->matchRequest($request);

            $action     = $matched["action"];
            $controller = $matched["controller"];
            $this->executeActionsFor($controller, $action, Before::class);
            $controller = $this->getContainer()->get($controller);
            $actionReturn = call_user_func_array([$controller, $action], [$request, $response]);
            $this->executeActionsFor($controller, $action, After::class, $actionReturn);
        } catch (ResourceNotFoundException $e) {
            $response->setStatusCode(404);
        }

        return $response;
    }

    private function executeActionsFor($controller, $action, $filterClass, $data = null)
    {
        $methodAnnotations = $this->getReader()->getMethodAnnotationsFor($controller, $action, $filterClass);
        $this->executeSteps($methodAnnotations, [$this, __FUNCTION__], $filterClass, $data);

        $classAnnotations = $this->getReader()->getClassAnnotationsFor($controller, $filterClass);
        $this->executeSteps($classAnnotations, [$this, __FUNCTION__], $filterClass, $data);
    }

    private function executeSteps(array $annotations, Callable $method, $filterClass, $data = null)
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

    public function setReader(HookReader $reader)
    {
        $this->reader = $reader;
        return $this;
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
}
