<?php
namespace Corley\Middleware\Reader;

use ReflectionClass;
use ReflectionMethod;
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
            $this->reader->getClassAnnotations($refl), function ($value) use ($instanceOf) {
                return ($value instanceof $instanceOf) ? true : false;
            });
    }

    public function getMethodAnnotationsFor($clazz, $method, $instanceOf)
    {
        $refl = new ReflectionMethod($clazz, $method);

        return array_filter(
            $this->reader->getMethodAnnotations($refl), function ($value) use ($instanceOf) {
                return ($value instanceof $instanceOf) ? true : false;
            }
        );
    }
}
