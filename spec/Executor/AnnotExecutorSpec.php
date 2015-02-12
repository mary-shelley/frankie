<?php

namespace spec\Corley\Middleware\Executor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Corley\Middleware\Reader\HookReader;
use Interop\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Corley\Demo\Controller\Index;
use Corley\Middleware\Annotations\Before;

class AnnotExecutorSpec extends ObjectBehavior
{
    function let(ContainerInterface $container, HookReader $reader)
    {
        $this->beConstructedWith($container, $reader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Corley\Middleware\Executor\AnnotExecutor');
    }

    function it_should_execute_just_the_action(
        ContainerInterface $container, HookReader $reader,
        Request $request, Response $response, Index $controller
    )
    {
        $container->get("Corley\\Demo\\Controller\\Index")->willReturn($controller);

        $reader->getMethodAnnotationsFor(Argument::Any(), Argument::Any(), Argument::Any())->willReturn([]);
        $reader->getClassAnnotationsFor(Argument::Any(), Argument::Any())->willReturn([]);

        $controller->index($request, $response)->shouldBeCalledTimes(1);
        $matched = [
            "controller" => "Corley\Demo\Controller\Index",
            "action" => "index",
        ];

        $this->execute($request, $response, $matched);
    }

    function it_should_execute_the_action_and_before_steps_for_methods(
        ContainerInterface $container, HookReader $reader,
        Request $request, Response $response, Index $controller
    )
    {
        $container->get("Corley\\Demo\\Controller\\Index")->willReturn($controller);

        $reader->getMethodAnnotationsFor(Argument::Any(), "index", Before::class)
            ->willReturn([
                (object)[
                    "targetClass" => "Corley\\Demo\\Controller\\Index",
                    "targetMethod" => "far",
                ]
            ]);
        $reader->getMethodAnnotationsFor(Argument::Any(), Argument::Any(), Argument::Any())->willReturn([]);
        $reader->getClassAnnotationsFor(Argument::Any(), Argument::Any())->willReturn([]);

        $controller->index($request, $response, Argument::Any())->shouldBeCalledTimes(1);
        $controller->far($request, $response, Argument::Any())->shouldBeCalledTimes(1);
        $matched = [
            "controller" => "Corley\Demo\Controller\Index",
            "action" => "index",
        ];

        $this->execute($request, $response, $matched);
    }
}
