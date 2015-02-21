<?php

namespace Corley\Demo\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Zend\EventManager\EventManager;

class IndexSpec extends ObjectBehavior
{
    public function let(EventManager $eventManager)
    {
        $this->setEventManager($eventManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Corley\Demo\Controller\Index');
    }

    public function it_should_prepare_the_bag(Request $request, Response $response, ResponseHeaderBag $bag)
    {
        $response->headers = $bag;
        $bag->set("content-type", "application/json")->shouldBeCalledTimes(1);

        $this->test($request, $response);

        $this->getBag()->shouldBe(["test" => "Ok"]);
    }

    public function it_should_trigger_the_mark_it_event_during_the_far_call(EventManager $eventManager)
    {
        $eventManager->trigger("mark-it", Argument::Any())->shouldBeCalledTimes(1);

        $this->far();
    }
}
