<?php
namespace Corley\Middleware\Loader;

use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Routing\Route;


class FrankieAnnotationClassLoader extends AnnotationClassLoader
{
    protected function configureRoute(Route $route, ReflectionClass $class, ReflectionMethod $method, $annot)
    {
        $route->setOption("_controller", $class->name);
        $route->setOption("_method", $method->name);

        return $route;
    }
}

