<?php
namespace Corley\Demo\Controller\Tests;

use Corley\Middleware\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Corley\Middleware\Annotations\After;
use Corley\Middleware\Annotations\Before;
use Zend\EventManager\EventManager;

/**
 * @Before(targetClass="Corley\Demo\Controller\Tests\Http", targetMethod="deny")
 */
class Six
{
    /**
     * @Route("/deny")
     */
    public function action()
    {
        echo __CLASS__ . "::" . __FUNCTION__ . PHP_EOL;
    }
}

