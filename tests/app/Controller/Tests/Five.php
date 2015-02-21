<?php
namespace Corley\Demo\Controller\Tests;

use Corley\Middleware\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Corley\Middleware\Annotations\After;
use Corley\Middleware\Annotations\Before;
use Zend\EventManager\EventManager;

class Five
{
    /**
     * @Route("/flow")
     * @Before(targetClass="Corley\Demo\Controller\Tests\One", targetMethod="action")
     * @After(targetClass="Corley\Demo\Controller\Tests\Three", targetMethod="action")
     */
    public function action()
    {
        echo __CLASS__ . "::" . __FUNCTION__ . PHP_EOL;
    }
}

