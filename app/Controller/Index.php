<?php
namespace Corley\Demo\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Index
{
    /**
     * @Route("/")
     */
    public function index(Request $request, Response $response)
    {
        $response->setContent("Hello World");
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {

    }
}
