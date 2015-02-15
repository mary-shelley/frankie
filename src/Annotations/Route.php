<?php
namespace Corley\Middleware\Annotations;

use Symfony\Component\Routing\Annotation\Route as BaseRoute;

/** @Annotation */
class Route extends BaseRoute
{
    public static function __set_state(array $state)
    {
        return new self($state);
    }
}
