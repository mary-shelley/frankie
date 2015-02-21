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
         * @Route("/somewhere/over/the/rainbow")
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

