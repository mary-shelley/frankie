<?php
namespace Corley\Demo\Controller;

use Corley\Middleware\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Corley\Middleware\Annotations\After;
use Corley\Middleware\Annotations\Before;
use Zend\EventManager\EventManager;

class Index
{
    private $bag;

    /**
     * @Inject
     * @var Zend\EventManager\EventManager
     */
    private $eventManager;

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
        $this->eventManager->trigger("mark-it", ["event" => "far"]);
    }

    /**
     * @Route("/param/{one}/param/{two}")
     * @Before(targetClass="Corley\Demo\Controller\Index", targetMethod="index")
     * @After(targetClass="Corley\Demo\Controller\Index", targetMethod="toJson")
     */
    public function param($request, $response, $one, $two)
    {
        return ["one" => $one, "two" => $two];
    }

    /**
     * @Route("/step")
     * @After(targetClass="Corley\Demo\Controller\Index", targetMethod="toJson")
     */
    public function step()
    {
        return ["response" => "OK"];
    }

    public function toJson(Request $request, Response $response, $data)
    {
        $response->headers->set("Content-Type", "application/json");
        $response->setContent(json_encode($data));
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

    public function setEventManager(EventManager $em)
    {
        $this->eventManager = $em;
    }
}
