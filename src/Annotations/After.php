<?php
namespace Corley\Middleware\Annotations;

/** @Annotation */
class After
{
    public $targetClass;
    public $targetMethod;
}
