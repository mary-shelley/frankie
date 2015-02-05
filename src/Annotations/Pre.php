<?php
namespace Corley\Middleware\Annotations;

/** @Annotation */
class Pre
{
    public $targetClass;
    public $targetMethod;
}
