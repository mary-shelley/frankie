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

This procedure can be applied to any step of action.

