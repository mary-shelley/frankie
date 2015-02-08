<?php
namespace Corley\Demo\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Corley\Middleware\Annotations\Before;

class Index
{
    private $bag;

    /**
     * @Route("/")
     * @Before(targetClass="Corley\Demo\Controller\Index", targetMethod="test")
     */
    public function index(Request $request, Response $response)
    {
        $response->setContent(json_encode($this->getBag()));
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(Request $request, Response $response)
    {
        $this->setBag(["test" => "Ok"]);
        $response->headers->set("content-type", "application/json");
    }

    /**
     * @Route("/far")
     * @Before(targetClass="Corley\Demo\Controller\Index", targetMethod="index")
     */
    public function far()
    {

    }

    public function getBag()
    {
        return $this->bag;
    }

    public function setBag($bag)
    {
        $this->bag = $bag;
        return $this;
    }

}
