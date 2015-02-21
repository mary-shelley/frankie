Dependency Injection
====================

Frankie uses .. `Acclimate` (https://github.com/jeremeamia/acclimate-container)  as container. In that way you can mixing different
service containers using a single entry point.

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
        private $a;

        /**
         * @Inject
         * @var A
         */
        public function setA($a)
        {
            $this->a = $a;
        }

        /**
         * @Before(targetClass="A", targetMethod="step")
         */
        public function action()
        {
            // will echo "Hello World"
            return implode(" ", $this->a->getBag());
        }
    }

And your spec could be something like:


.. code-block:: php

    <?php
    class BSpec
    {
        function let(A $a)
        {
            $this->setA($a);
        }

        function it_should_say_hello(A $a)
        {
            $a->willReturn(["Hello", "World"]);

            $this->action()->shouldReturn("Hello World");
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

where Request is an `Symfony\\Component\\HttpFoundation\\Request` and the Response
is an `Symfony\\Component\\HttpFoundation\\Response`

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

With PHP 5.6 could be interesting apply variadics to actions, like:

.. code-block:: php

    <?php
    class A
    {
        /**
         * @Route("/path/{act}/met/{oth}")
         */
        public function method(Request $request, Response $response, ...$params){}
    }

Or even more

.. code-block:: php

    <?php
    class A
    {
        /**
         * @Route("/path/{act}/met/{oth}")
         */
        public function method(...$params)
        {
            //$params[0] <- request
            //$params[1] <- response
            //$params[2] <- act
            //$params[3] <- oth
        }
    }

But actually seems that we have some problem with annotation parsing, check this
out: https://github.com/symfony/symfony/pull/13690
