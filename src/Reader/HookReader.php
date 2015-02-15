<?php
namespace Corley\Middleware\Reader;

use ReflectionClass;
use ReflectionMethod;
use Corley\Middleware\Annotations\After;
use Corley\Middleware\Annotations\Before;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;

class HookReader
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getClassAnnotationsFor($clazz, $instanceOf)
    {
        $refl = new ReflectionClass($clazz);
        return array_filter(
            $this->reader->getClassAnnotations($refl), function($value) use ($instanceOf) {
                return ($value instanceOf $instanceOf) ? true : false;
            });
    }

    public function getMethodAnnotationsFor($clazz, $method, $instanceOf)
    {
        $refl = new ReflectionMethod($clazz, $method);
        return array_filter(
            $this->reader->getMethodAnnotations($refl), function($value) use ($instanceOf) {
                return ($value instanceOf $instanceOf) ? true : false;
            }
        );
    }
}
