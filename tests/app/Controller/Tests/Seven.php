<?php
namespace Corley\Demo\Controller\Tests;

use Corley\Middleware\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Corley\Middleware\Annotations\After;
use Corley\Middleware\Annotations\Before;
use Zend\EventManager\EventManager;

class Seven
{
    /**
     * @Route("/close-direct")
     * @After(targetClass="Corley\Demo\Controller\Tests\Six", targetMethod="action")
     */
    public function action(Request $request, Response $response)
    {
        echo __CLASS__ . "::" . __FUNCTION__ . PHP_EOL;

        $response->setStatusCode(202);
        return $response;
    }

    /**
     * @Route("/close-after")
     * @After(targetClass="Corley\Demo\Controller\Tests\Http", targetMethod="deny")
     * @After(targetClass="Corley\Demo\Controller\Tests\Seven", targetMethod="action")
     */
    public function pass(Request $request, Response $response)
    {
        echo __CLASS__ . "::" . __FUNCTION__ . PHP_EOL;
    }
}


