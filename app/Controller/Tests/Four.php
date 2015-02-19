<?php
namespace Corley\Demo\Controller\Tests;

use Corley\Middleware\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Corley\Middleware\Annotations\After;
use Zend\EventManager\EventManager;

class Four
{
    public function methodB()
    {
        echo __CLASS__ . "::" . __FUNCTION__ . PHP_EOL;
    }
}


