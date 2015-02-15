<?php
namespace Corley\Middleware\Factory;

use Acclimate\Container\CompositeContainer as Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Annotations\AnnotationReader;
use Corley\Middleware\Loader\RouteAnnotationClassLoader;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Corley\Middleware\Reader\HookReader;
use Corley\Middleware\Executor\AnnotExecutor;
use Corley\Middleware\App;

class AppFactory
{
    public static function createApp($sourceFolder, Container $container, Request $request = null, Response $response = null)
    {
        if (!$request) {
            $request = Request::createFromGlobals();
        }

        if (!$response) {
            $response = new Response();
        }

        $reader = new AnnotationReader();

        $routeLoader = new RouteAnnotationClassLoader($reader);
        $loader = new AnnotationDirectoryLoader(new FileLocator([$sourceFolder]), $routeLoader);
        $routes = $loader->load($sourceFolder);

        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($routes, $context);

        $hookReader = new HookReader($reader);

        $executor = new AnnotExecutor($container, $hookReader);

        $app = new App($matcher, $executor);

        return $app;
    }
}
