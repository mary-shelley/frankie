# Frankie - A frankenstein micro-framework for PHP

 * Develop: [![Build Status](https://travis-ci.org/wdalmut/frankie.svg?branch=develop)](https://travis-ci.org/wdalmut/frankie)

The goal is focus on actions and attach before and action events using
annotations

```php
<?php
class MyController
{
    /**
     * @Inject
     * @var Zend\EventManager\EventManager
     */
    private $eventManager;

    /**
     * @Route("/my/path", methods={"GET"})
     * @Before(targetClass="MyHook\ThisOne", targetMethod="count")
     */
    public function get(Request $request, Response $response)
    {
        // ...
        $this->eventManager->trigger("mark-it", $element);
        // ...
    }
}

```

The goal is enforce the testing practices using SpecBDD approach.

```php
<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zend\EventManager\EventManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyControllerSpec extends ObjectBehavior
{
    function let(EventManager $em)
    {
        $this->setEventManager($em);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('MyController');
    }

    function it_should_trigger_the_mark_event(
        Request $request, Response $response, EventManager $em
    )
    {
        $em->trigger("mark-it", Argument::Any())->shouldBeCalledTimes(1);

        $this->get($request, $response);
    }
}
```

