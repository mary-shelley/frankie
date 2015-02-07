<?php
namespace Corley\Middleware;

use DI\Container;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class App
{
    private $container;
    private $router;

    public function __construct(Container $container)
    {
        $this->container = $container;
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

    public function getContainer()
    {
        return $this->container;
    }

    public function run(Request $request, Response $response)
    {
        try {
            $matched = $this->getRouter()->matchRequest($request);

            $action     = $matched["action"];
            $controller = $this->getContainer()->get($matched["controller"]);
            $actionReturn = call_user_func_array([$controller, $action], [$request, $response]);
        } catch (ResourceNotFoundException $e) {
            $response->setStatusCode(404);
        }

        $response->send();
    }
}
