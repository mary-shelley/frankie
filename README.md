# Frankie - A frankenstein micro-framework for PHP

 * Develop: [![Build Status](https://travis-ci.org/wdalmut/frankie.svg?branch=develop)](https://travis-ci.org/wdalmut/frankie)

## Features

Frankie is a micro-framework focused on annotation. The goal is to use
annotation in order to do almost everything in the framework.

 * Annotated Routes (Routing)
 * Annotated Injections (Dependencies)
 * Annotated Request Flow (Application Flow)

Mainly Frankie is a framework for create RESTful applications and microservices.

Discover more on the [documentation](http://frankie.readthedocs.org/)

## Hands-on!

The goal is focus on actions and attach before and action events using
annotations

```php
<?php
/**
 * @Before(targetClass="HttpAuth", targetMethod="basic")
 * @After(targetClass="Serializer", targetMethod="toJson")
 */
class MyController
{
    /**
     * @Inject
     * @var Zend\EventManager\EventManager
     */
    private $eventManager;

    /**
     * @Route("/my/path/{id}", methods={"GET"})
     * @Before(targetClass="MyHook\ThisOne", targetMethod="count")
     * @Before(targetClass="Stopwatch", targetMethod="start")
     * @After(targetClass="Stopwatch", targetMethod="stop")
     */
    public function get(Request $request, Response $response, $id)
    {
        // ...
        $this->eventManager->trigger("mark-it", $element);
        // ...
    }
}

```

### Testing with SpecBDD - PHPSpec

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


