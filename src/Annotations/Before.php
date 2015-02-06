<?php
namespace Corley\Middleware\Annotations;

/** @Annotation */
class Before
{
    public $targetClass;
    public $targetMethod;
}
