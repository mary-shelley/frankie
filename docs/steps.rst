Steps - Events
==============

The goal of Frankie is keep all things as simple as possible. For that reason,
mainly it just execute the action expressed by a route path.

When we have to interact with any step before or after our route we can just
indicate steps with annotations

.. code-block:: php

    <?php
    /**
     * @Before(targetClass="ClassB", targetMethod="methodB")
     * @After(targetClass="ClassC", targetMethod="methodC")
     */
    class MyClass
    {
        /**
         * @Route("/", method={"GET"})
         * @Before(targetClass="ClassD", targetMethod="methodD")
         * @After(targetClass="ClassE", targetMethod="methodE")
         */
        public function action() { }
    }

Frankie execute steps using before and after annotations on methods and class
definitions.

The flow that Frankie executes is:

 * ClassB::methodB
 * ClassD::methodD
 * MyClass::action
 * ClassE::methodE
 * ClassC::methodC

Of course you can have different Before and After steps for every step that
you execute and you can have more Before and After per method/class definition.

.. code-block::php

    <?php
    /**
     * @Before(targetClass="ClassB", targetMethod="methodB")
     * @Before(targetClass="ClassF", targetMethod="methodF")
     * @After(targetClass="ClassC", targetMethod="methodC")
     */
    class MyClass
    {
        /**
         * @Route("/", method={"GET"})
         * @Before(targetClass="ClassD", targetMethod="methodD")
         * @Before(targetClass="ClassG", targetMethod="methodG")
         * @After(targetClass="ClassE", targetMethod="methodE")
         */
        public function action() { }
    }

    class ClassG
    {
        /**
         * @Route("/", method={"GET"})
         * @Before(targetClass="ClassH", targetMethod="methodH")
         * @Before(targetClass="ClassI", targetMethod="methodI")
         */
        public function methodG() { }
    }

In this case:

 * ClassF::methodF
 * ClassB::methodB
 * ClassG::methodG
 * ClassH::methodH
 * ClassI::methodI
 * ClassD::methodD
 * MyClass::action
 * ClassE::methodE
 * ClassC::methodC


Definitions
-----------

Every class is resolved using the dependency injection container, for that
reason any step could be a service and we can use the single reference in order
to pass data during our steps.

.. code-block:: php

    <?php
    class A
    {
        private $bag;

        public function getBag()
        {
            return $this->bag;
        }

        public function step()
        {
            $this->bag = ["Hello", "World"];
        }
    }

And the action is something like:

.. code-block:: php

    <?php
    class B
    {
        /**
         * @Inject
         * @var A
         */
        private $a;

        /**
         * @Before(targetClass="A", targetMethod="step")
         */
        public function Action()
        {
            // will echo "Hello World"
            echo implode(" ", $this->a->getBag());
        }
    }

Parameters
----------

The framework pass always the request and response to your action and any other
request parameter, look this example:

.. code-block:: php

    <?php
    class A
    {
        /**
         * @Route("/")
         */
        public function method(Request $request, Response $response){}
    }

where Request is an `Symfony\Component\HttpFoundation\Request` and the Response
is an `Symfony\Component\HttpFoundation\Response`

In addition if your route uses parameters, those are passed to your method

.. code-block:: php

    <?php
    class A
    {
        /**
         * @Route("/path/{act}/met/{oth}")
         */
        public function method(Request $request, Response $response, $act, $oth){}
    }


