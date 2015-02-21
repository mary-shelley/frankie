<?php
namespace Corley\Demo\Controller\Tests;

use Corley\Middleware\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Corley\Middleware\Annotations\After;
use Zend\EventManager\EventManager;

/**
 * @After(targetClass="Corley\Demo\Controller\Tests\Four", targetMethod="methodB")
 */
class Three
{
    /**
     * @Route("/after-flow")
     * @After(targetClass="Corley\Demo\Controller\Tests\Three", targetMethod="methodC")
     */
    public function action()
    {
        echo __CLASS__ . "::" . __FUNCTION__ . PHP_EOL;
    }

    public function methodC()
    {
        echo __CLASS__ . "::" . __FUNCTION__ . PHP_EOL;
    }
}

