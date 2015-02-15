<?php
namespace Corley\Middleware\Loader;

use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Routing\Route;

class RouteAnnotationClassLoader extends AnnotationClassLoader
{
    protected function configureRoute(Route $route, ReflectionClass $class, ReflectionMethod $method, $annot)
    {
        $defaults = array('annotation' => $annot, 'controller' => $class->name, 'action' => $method->name);
        $route->setDefaults($defaults);
    }
}
