<?php

namespace spec\Corley\Middleware;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Corley\Middleware\Executor\AnnotExecutor;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AppSpec extends ObjectBehavior
{
    function let(UrlMatcher $matcher, AnnotExecutor $executor)
    {
        $this->beConstructedWith($matcher, $executor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Corley\Middleware\App');
    }

    function it_should_execute_matched_route(
        UrlMatcher $matcher, AnnotExecutor $executor,
        Request $request, Response $response
    ) {
        $matched = ["controller" => "A", "action" => "B"];
        $matcher->matchRequest(Argument::Any())->willReturn($matched);
        $executor->execute(Argument::Any(), Argument::Any(), $matched)
            ->willReturn($response)
            ->shouldBeCalledTimes(1);

        $this->run($request, $response)->shouldBe($response);
    }

    function it_should_capture_missing_controller_errors_as_page_not_found(
        UrlMatcher $matcher, AnnotExecutor $executor,
        Request $request, Response $response
    ) {
        $matcher->matchRequest(Argument::Any())
            ->willThrow("Symfony\\Component\\Routing\\Exception\\ResourceNotFoundException");

        $response->setStatusCode(404)->shouldBeCalled();

        $this->run($request, $response)->shouldBe($response);
    }

    function it_should_capture_any_kind_of_error_as_internal_server_error(
        UrlMatcher $matcher, AnnotExecutor $executor,
        Request $request, Response $response
    ) {
        $matcher->matchRequest(Argument::Any())
            ->willThrow("Exception");

        $response->setStatusCode(500)->shouldBeCalled();

        $this->run($request, $response)->shouldBe($response);
    }
}
