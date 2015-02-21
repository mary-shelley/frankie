.. Frankie Framework documentation master file, created by
   sphinx-quickstart on Wed Feb 18 19:56:50 2015.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

Welcome to Frankie Framework's documentation!
=============================================

Frankie is a Frankenstein framework just because uses different base components
from other open-source projects like: PHP-Di, Symfony Routing, Doctrine
Annotations, Symfony HTTP Foundation and of course your other libraries!

Mainly it believes on a middleware approach in this way

.. code-block:: php

    <?php
    class IndexController
    {
        /**
         * @Route("/", methods={"GET"})
         */
        public function indexAction(Request $request, Response $response) { /** code */ }
    }

Yes, that's it! Nothing more your action is executed and you can deal
directly with the *Request* and the *Response*

.. code-block:: php

    <?php
    public function indexAction(Request $request, Response $response)
    {
        $response->setContent("Hello");
    }

Handle your application flow
----------------------------

The framework is not event driven... Wait... How can i engage any other step
like: authentication, serializers etc? Simple, defining steps:

.. code-block:: php

    <?php
    /**
     * @Before(targetClass="HttpAuth", targetMethod="basic")
     * @After(targetClass="Serializer", targetMethod="jsonify")
     */
    class IndexController
    {
        /**
         * @Before(targetClass="MyClass", targetMethod="aMethod")
         * @Route("/", methods={"GET"})
         */
        public function indexAction(Request $request, Response $response) { /** code */ }
    }

The framework uses extensively annotations in order to execute any other
required step, in this case the flow is:

 * HttpAuth::basic
 * MyClass::myMethod
 * IndexController::indexAction
 * Serializer::jsonify

Ofcourse if any other method and classes uses `Before` and `After` annotations
those will partecipate to the call stack.

All classes are simple POPO objects.

Dependency Injection
--------------------

All objects are provided using a Dependency Injection Container, in particular
the framework uses the Acclimate project in order to enable you to add any other
dependency inject container and by default the framework adds the `mnapoli`
`PHP-DI` that uses annotations for injections.

For example if you want to use an event manager (Zend Framework for example) you
can just inject it:

.. code-block:: php

    <?php
    /**
     * @Before(targetClass="Authentication", targetMethod="basic")
     * @After(targetClass="Serializer", targetMethod="jsonify")
     */
    class IndexController
    {
        /**
         * @Inject
         * @var Zend\EventManager\EventManager
         */
        private $eventManager;

        /**
         * @Before(targetClass="AnyClass", targetMethod="anyMethod")
         * @Route("/", methods={"GET"})
         */
        public function indexAction(Request $request, Response $response)
        {
            $this->eventManager->trigger("mark.it", ["any data" => "anything"]);

            return "ok";
        }
    }

All steps are provided using always the dependency injection container. That
means that you can define different containers using Acclimate like: ZF Service
Manager, Symfony DiC, Pimple and mnapoli and mixing all resources together.

Testing, -> SpecBDD
----------------------

With dependency injection we can speed up also our testing section, for example
using PHPSpec for behaviour driven development using specifications

.. code-block:: php

    <?php
    class IndexControllerSpec extends ObjectBehavior
    {
        public function let(EventManager $eventManager)
        {
            $this->setEventManager($eventManager);
        }

        public function it_is_initializable()
        {
            $this->shouldHaveType('Index');
        }

        public function it_should_trigger_the_mark_event(Request $request, Response $response)
        {
            $this->trigger("mark.it", Argument::Any())->shouldBeCalledTimes(1);

            $this->indexAction($request, $response)->shouldReturn("ok");
        }
    }

And more
--------

Discover more about Frankie in the documentation!

Contents:

.. toctree::
   :maxdepth: 2

   getting-started
   steps
   di



Indices and tables
==================

* :ref:`genindex`
* :ref:`modindex`
* :ref:`search`

