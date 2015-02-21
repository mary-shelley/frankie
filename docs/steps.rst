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

.. code-block:: php

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

Steps parameters
----------------

Before steps can use the matched array as third parameter

.. code-block:: php

    <?php
    class Hook
    {
        public function beforeStep(Request $request, Response $response, array $matched)
        {

        }
    }

After steps instead access to the action response as third parameter

.. code-block:: php

    <?php
    class Hook
    {
        public function afterStep(Request $request, Response $response, $data = null)
        {

        }
    }

Dependency Injection Container
------------------------------

All steps and actions are resolved by the dependency injection container. That
means that we can inject or preapre all of our steps without problems thanks to
our DiC.

.. code-block:: php

    <?php
    class Hook
    {
        /**
         * @Inject
         * @var Zend\Db\Adapter\Adapter;
         */
        $db;
    }

