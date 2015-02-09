<?php
namespace Corley\Middleware\Reader;

use ReflectionClass;
use ReflectionMethod;
use Doctrine\Common\Annotations\AnnotationReader;
use Corley\Middleware\Annotations\Before;

class HookReader
{
    private $reader;

    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }
    public function getBeforeClassAnnotations($clazz)
    {
        $refl = new ReflectionClass($clazz);
        return array_filter($this->reader->getClassAnnotations($refl), function($value) {return ($value instanceOf Before) ? true : false;});
    }

    public function getBeforeMethodAnnotations($clazz, $method)
    {
        $refl = new ReflectionMethod($clazz, $method);
        return array_filter($this->reader->getMethodAnnotations($refl), function($value) {return ($value instanceOf Before) ? true : false;});
    }
}
