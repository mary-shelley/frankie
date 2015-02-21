<?php
namespace Corley\Demo\Controller\Tests;

use Corley\Middleware\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Corley\Middleware\Annotations\After;
use Corley\Middleware\Annotations\Before;
use Zend\EventManager\EventManager;

/**
 * @Before(targetClass="Corley\Demo\Controller\Tests\Two", targetMethod="methodB")
 */
class One
{
    /**
     * @Route("/base-flow")
     * @Before(targetClass="Corley\Demo\Controller\Tests\One", targetMethod="methodC")
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
