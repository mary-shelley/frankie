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
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ApcCache;

class AppFactory
{
    public static $DEBUG = false;
    public static $CACHE_FOLDER = "/tmp";

    const ROUTE_CACHE_CLASS = "CachedUrlMatcher";
    const ROUTE_CACHE_FILE = "bootstrap.routes.cache.php";

    public static function createApp($sourceFolder, Container $container, Request $request = null, Response $response = null)
    {
        if (!$request) {
            $request = Request::createFromGlobals();
        }

        if (!$response) {
            $response = new Response();
        }

        $reader = new CachedReader(
            new AnnotationReader(),
            new ApcCache(),
            self::$DEBUG
        );

        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = self::createMatcher($sourceFolder, $reader, $context);

        $hookReader = new HookReader($reader);

        $executor = new AnnotExecutor($container, $hookReader);

        $app = new App($matcher, $executor);

        return $app;
    }

    private static function createMatcher($sourceFolder, $reader, $context)
    {
        $matcher = null;
        $routeCacheFile = self::$CACHE_FOLDER."/".self::ROUTE_CACHE_FILE;
        if (!self::$DEBUG) {
            if (!file_exists($routeCacheFile)) {
                $routeLoader = new RouteAnnotationClassLoader($reader);
                $loader = new AnnotationDirectoryLoader(new FileLocator([$sourceFolder]), $routeLoader);
                $routes = $loader->load($sourceFolder);

                $dumper = new PhpMatcherDumper($routes);
                file_put_contents($routeCacheFile, $dumper->dump(["class" => self::ROUTE_CACHE_CLASS]));

                $matcher = new UrlMatcher($routes, $context);
            } else {
                $routes = include self::$CACHE_FOLDER."/".self::ROUTE_CACHE_FILE;
                $className = self::ROUTE_CACHE_CLASS;
                $matcher = new $className($context);
            }
        } else {
            $routeLoader = new RouteAnnotationClassLoader($reader);
            $loader = new AnnotationDirectoryLoader(new FileLocator([$sourceFolder]), $routeLoader);
            $routes = $loader->load($sourceFolder);

            $matcher = new UrlMatcher($routes, $context);
        }

        return $matcher;
    }
}
