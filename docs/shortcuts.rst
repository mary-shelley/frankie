Shortcutting actions
====================

If you return a `Response` the framework stops immediately and will return that
response to the client.

For example during an authorization:

.. code-block:: php

    <?php
    class HttpAuth
    {
        public function basic()
        {
            // failed authorization

            $response = new Response();
            $response->setStatusCode(401);
            return $response;
        }
    }

Of course you can reuse the application response

.. code-block:: php

    <?php
    class HttpAuth
    {
        public function basic(Request $request, Response $response)
        {
            // failed authorization

            $response->setStatusCode(401);
            return $response;
        }
    }

This procedure can be applied to any step or action.

Inheritance on Responses
------------------------

If you image to extends the Response class in order to obtain different
definitions like:

.. code-block:: php

    <?php
    class ApiProblem extends Response
    {
        public function __construct($statusCode, $content = "") {...}
    }

You can use as

.. code-block:: php

    <?php
    class HttpAuth
    {
        public function basic()
        {
            // failed authorization
            return new ApiProblem(401, "Unauthorized!");
        }
    }

That's more interesting!
