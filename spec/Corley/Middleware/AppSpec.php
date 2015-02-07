<?php

namespace spec\Corley\Middleware;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use DI\Container;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Corley\Demo\Controller\Index;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class AppSpec extends ObjectBehavior
{
    public function let(Container $container, UrlMatcher $router)
    {
        $this->beConstructedWith($container);
        $this->setRouter($router);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Corley\Middleware\App');
    }

    public function it_should_call_an_action(
        Container $container, UrlMatcher $router, Index $controller,
        Request $request, Response $response
    ) {
        $router->matchRequest($request)->willReturn([
            "controller" => "Corley\\Demo\\Controller\\Index",
            "action" => "index",
        ]);

        $container->get("Corley\\Demo\\Controller\\Index")->willReturn($controller);
        $controller->index(Argument::Any(), Argument::Any())->shouldBeCalledTimes(1);

        $response->send()->shouldBeCalledTimes(1);

        $this->run($request, $response);
    }

    public function it_should_notify_a_missing_action(UrlMatcher $router, Request $request, Response $response)
    {
        $router->matchRequest($request)->willThrow(new ResourceNotFoundException());
        $response->setStatusCode(404)->shouldBeCalled();

        $response->send()->shouldBeCalledTimes(1);

        $this->run($request, $response);
    }
}
