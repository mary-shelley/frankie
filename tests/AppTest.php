<?php
namespace Corley\Middleware;

use DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Corley\Demo\Controller\Index;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationReader;
use Corley\Middleware\Reader\HookReader as Reader;
use Corley\Middleware\Annotations\After;

class AppTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $router;
    private $response;

    public function setUp()
    {
        $loader = require __DIR__.'/../vendor/autoload.php';
        AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

        $this->container = $this->prophesize("DI\\Container");
        $this->router = $this->prophesize("Symfony\\Component\\Routing\\Matcher\\UrlMatcher");
        $this->response = new Response();
    }

    public function testSimpleCorrectFlow()
    {
        $this->router->matchRequest(Argument::Any())->willReturn(["controller" => "Corley\\Demo\\Controller\\Index", "action" => "test"]);

        $this->container->get(Argument::Any())->willReturn(new Index());

        $app = new App($this->container->reveal());
        $app->setReader(new Reader(new AnnotationReader()));
        $app->setRouter($this->router->reveal());

        $request = Request::create("/");
        $app->run($request, $this->response);

        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function test404ErrorPage()
    {
        $this->router->matchRequest(Argument::Any())->willThrow("Symfony\\Component\\Routing\\Exception\\ResourceNotFoundException");

        $app = new App($this->container->reveal());
        $app->setReader(new Reader(new AnnotationReader()));
        $app->setRouter($this->router->reveal());

        $request = Request::create("/");
        $app->run($request, $this->response);

        $this->assertEquals(404, $this->response->getStatusCode());
    }

    public function testBeforeHookIsCalled()
    {
        $this->router->matchRequest(Argument::Any())
            ->willReturn(["controller" => "Corley\\Demo\\Controller\\Index", "action" => "index"]);

        $index = $this->prophesize('Corley\\Demo\\Controller\\Index');
        $index->index(Argument::Any(), Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);
        $index->test(Argument::Any(), Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);

        $this->container->get("Corley\\Demo\\Controller\\Index")->willReturn($index->reveal());

        $request = Request::create("/");

        $app = new App($this->container->reveal());
        $app->setReader(new Reader(new AnnotationReader()));
        $app->setRouter($this->router->reveal());

        $app->run($request, $this->response);

        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function testBeforeHookIsACallChain()
    {
        $this->router->matchRequest(Argument::Any())
            ->willReturn(["controller" => "Corley\\Demo\\Controller\\Index", "action" => "far"]);

        $index = $this->prophesize('Corley\\Demo\\Controller\\Index');
        $index->index(Argument::Any(), Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);
        $index->test(Argument::Any(), Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);
        $index->far(Argument::Any(), Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);

        $this->container->get("Corley\\Demo\\Controller\\Index")->willReturn($index->reveal());

        $request = Request::create("/");

        $app = new App($this->container->reveal());
        $app->setReader(new Reader(new AnnotationReader()));
        $app->setRouter($this->router->reveal());

        $app->run($request, $this->response);

        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function testBeforeHookIsACallChainOverClasses()
    {
        $this->router->matchRequest(Argument::Any())
            ->willReturn(["controller" => "Corley\\Demo\\Controller\\My", "action" => "act"]);

        $index = $this->prophesize('Corley\\Demo\\Controller\\Index');
        $index->index(Argument::Any(), Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);
        $index->test(Argument::Any(), Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);
        $index->far(Argument::Any(), Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);

        $far = $this->prophesize('Corley\\Demo\\Controller\\My');
        $far->act(Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);

        $this->container->get("Corley\\Demo\\Controller\\My")->willReturn($far->reveal());
        $this->container->get("Corley\\Demo\\Controller\\Index")->willReturn($index->reveal());

        $request = Request::create("/");

        $app = new App($this->container->reveal());
        $app->setReader(new Reader(new AnnotationReader()));
        $app->setRouter($this->router->reveal());

        $app->run($request, $this->response);

        $this->assertEquals(200, $this->response->getStatusCode());
    }
}
