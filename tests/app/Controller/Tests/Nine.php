<?php
namespace Corley\Demo\Controller\Tests;

use Corley\Middleware\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Corley\Middleware\Annotations\After;
use Corley\Middleware\Annotations\Before;
use Zend\EventManager\EventManager;

/**
 * @Before(targetClass="Corley\Demo\Controller\Tests\Four", targetMethod="methodB")
 * @After(targetClass="Corley\Demo\Controller\Tests\Four", targetMethod="methodB")
 */
class Nine implements Authenticable, Serializable
{
    /**
     * @Route("/long-with-interface")
     */
    public function action()
    {
        echo __CLASS__ . "::" . __FUNCTION__ . PHP_EOL;
    }
}

