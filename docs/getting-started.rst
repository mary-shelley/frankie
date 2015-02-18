Getting Started
===============

Getting started with Frankie is super-simple, you can create the base project
using composer:

.. code-block:: shell

    php composer.phar create-project wdalmut/frankie-app path dev-develop

Now just bring up your dev server:

.. code-block:: shell

    php -S localhost:8080 -t web

And go using your browser to: "http://localhost:8080/"

How the project works
---------------------

The project uses `composer` in order to download dependencies and you can
add any other projects using the `composer.json` as usual.
The `web` folder includes the index file that embeds the `Symfony Container` and
the `PHP-Di` container together using the `Acclimate` project. In this way we
can configure any other dependencies using the Yaml syntax and the SF DiC and
our injections directly using the PHP-Di annotations.

We also have a `cache` folder in order to speed-up all framework internals.

That's it...

How to start with the framework
-------------------------------

If you use PHPSpec just describe an object behavior:

.. code-block:: shell

    ./vendor/bin/phpspec describe Controller/MyController

Now PHPSpec creates for us the `MyController` specification file:

.. code-block:: php

    <?php

    namespace spec\Controller;

    use PhpSpec\ObjectBehavior;
    use Prophecy\Argument;

    class MyControllerSpec extends ObjectBehavior
    {
        function it_is_initializable()
        {
            $this->shouldHaveType('MyController');
        }
    }

If you try to run specs with:

.. code-block:: shell

    ./vendor/bin/phpspec run

We can also create the `MyController` source file:

.. code-block:: php

    <?php
    namespace Controller;

    class MyController
    {
    }

By default the framework serialize the action return value in a json format,
just add a simple specification (`MyControllerSpec`):

.. code-block:: php

    <?php
    function it_should_return_an_hello_message(
        Request $request, Response $response
    )
    {
        $this->helloAction()->shouldReturn("hello world");
    }

Running specs again we can check that our expectations fails... Just write our
action in `MyController`:

.. code-block:: php

    <?php
    public function helloAction()
    {
        return "hello world";
    }

Run again our specification in order to see that expectations works!

Now the HTTP flow
-----------------

We never define the `route` path in order to check also the flow with the
browser or any other http client or a functional test, in order to create a
personal path, we just have to apply our first annotation:

.. code-block:: php

    <?php
    /**
     * @Route("/hello")
     * @After(targetClass="Serializer\Json", targetMethod="serialize")
     */
    public function helloAction()

Remember that you have to include annotations that we use, in this case:

.. code-block:: php

    <?php
    use Corley\Middleware\Annotations\Route;
    use Corley\Middleware\Annotations\After;

    ...
Just navigate: `http://localhost:8080/hello` to see our `hello world` message!
