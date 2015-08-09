<?php
namespace Corley\Demo56\Controller;

use Corley\Middleware\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Corley\Middleware\Annotations\After;
use Corley\Middleware\Annotations\Before;
use Zend\EventManager\EventManager;

class VariadicController
{
    /**
     * @Route("/user/{id}/company/{cid}/support/{status}/status")
     */
    public function tooManyArguments(Request $request, Response $response, ...$args)
    {
        echo json_encode($args);
    }
}
