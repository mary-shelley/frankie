<?php
namespace Corley\Middleware;

use DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Corley\Demo\Controller\Index;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Prophecy\Argument;

class AppTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $router;
    private $response;

    public function setUp()
    {
        $loader = require __DIR__ . '/../vendor/autoload.php';
        AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

        $this->container = $this->prophesize("DI\\Container");
        $this->router = $this->getMockBuilder("Symfony\\Component\\Routing\\Matcher\\UrlMatcherInterface")->getMock();
        $this->response = $this->getMockBuilder("Symfony\\Component\\HttpFoundation\\Response")
            ->setMethods(["send"])
            ->getMock();
        $this->response->method("send")->willReturn(null);
    }

    public function testSimpleCorrectFlow()
    {
        $this->router->method("match")
            ->will($this->returnValue(["controller" => "Corley\\Demo\\Controller\\Index", "action" => "test"]));

        $this->container->get(Argument::Any())->willReturn(new Index());

        $app = new App($this->container->reveal());
        $app->setRouter($this->router);

        $request = Request::create("/");
        $app->run($request, $this->response);

        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function test404ErrorPage()
    {
        $this->router->method("match")->will($this->throwException(new ResourceNotFoundException()));

        $app = new App($this->container->reveal());
        $app->setRouter($this->router);

        $request = Request::create("/");
        $app->run($request, $this->response);

        $this->assertEquals(404, $this->response->getStatusCode());
    }

    public function testBeforeHookIsCalled()
    {
        $this->router->method("match")
            ->will($this->returnValue(["controller" => "Corley\\Demo\\Controller\\Index", "action" => "index"]));

        $index = $this->prophesize('Corley\\Demo\\Controller\\Index');
        $index->index(Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);
        $index->test(Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);

        $this->container->get("Corley\\Demo\\Controller\\Index")->willReturn($index->reveal());

        $request = Request::create("/");

        $app = new App($this->container->reveal());
        $app->setRouter($this->router);

        $app->run($request, $this->response);

        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function testBeforeHookIsACallChain()
    {
        $this->router->method("match")
            ->will($this->returnValue(["controller" => "Corley\\Demo\\Controller\\Index", "action" => "far"]));

        $index = $this->prophesize('Corley\\Demo\\Controller\\Index');
        $index->index(Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);
        $index->test(Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);
        $index->far(Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);

        $this->container->get("Corley\\Demo\\Controller\\Index")->willReturn($index->reveal());

        $request = Request::create("/");

        $app = new App($this->container->reveal());
        $app->setRouter($this->router);

        $app->run($request, $this->response);

        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function testBeforeHookIsACallChainOverClasses()
    {
        $this->router->method("match")
            ->will($this->returnValue(["controller" => "Corley\\Demo\\Controller\\My", "action" => "act"]));

        $index = $this->prophesize('Corley\\Demo\\Controller\\Index');
        $index->index(Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);
        $index->test(Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);
        $index->far(Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);

        $far = $this->prophesize('Corley\\Demo\\Controller\\My');
        $far->act(Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);

        $this->container->get("Corley\\Demo\\Controller\\My")->willReturn($far->reveal());
        $this->container->get("Corley\\Demo\\Controller\\Index")->willReturn($index->reveal());

        $request = Request::create("/");

        $app = new App($this->container->reveal());
        $app->setRouter($this->router);

        $app->run($request, $this->response);

        $this->assertEquals(200, $this->response->getStatusCode());
    }
}
