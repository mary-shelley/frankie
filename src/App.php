<?php
namespace Corley\Middleware;

use DI\Container;
use Symfony\Component\Routing\RequestContext;
use Doctrine\Common\Annotations\AnnotationReader;
use Corley\Middleware\Loader\FrankieAnnotationClassLoader;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class App
{
    private $request;
    private $response;
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function run($path)
    {
        $reader = new AnnotationReader();
        $frankieLoader = new FrankieAnnotationClassLoader($reader);
        $loader = new AnnotationDirectoryLoader(new FileLocator([$path]), $frankieLoader);
        $routes = $loader->load($path);

        $context = new RequestContext();
        $context->fromRequest($this->getRequest());
        $matcher = new UrlMatcher($routes, $context);

        $matched = $matcher->matchRequest($this->getRequest());

        $action     = $matched["action"];
        $controller = $this->getContainer()->get($matched["controller"]);
        $response = call_user_func_array([$controller, $action], [$this->getRequest(), $this->getResponse()]);

        $this->getResponse()->send();
    }
}
