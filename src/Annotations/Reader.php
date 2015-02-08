<?php
namespace Corley\Middleware\Annotations;

use ReflectionClass;
use ReflectionMethod;
use Doctrine\Common\Annotations\AnnotationReader;

class Reader extends AnnotationReader
{
    public function getBeforeClassAnnotations(ReflectionClass $refl)
    {
        return array_filter($this->getClassAnnotations($refl), function($value) {return ($value instanceOf Before) ? true : false;});
    }

    public function getBeforeMethodAnnotations(ReflectionMethod $refl)
    {
        return array_filter($this->getMethodAnnotations($refl), function($value) {return ($value instanceOf Before) ? true : false;});
    }
}
