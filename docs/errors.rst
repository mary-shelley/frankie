Handling Errors
---------------

Frankie can't react to 404 pages (no routes) because there is no action to analyze and
preapare and also for any 500 errors (other exceptions).

For that reason the app expose a method in order to register a callable for this
kind of situations.

.. code-block:: php

    <?php
    $app = Factory\AppFactory::create(...);
    $app->setErrorHandler(function(Request $request, Response $response, $e) {
        $response->setContent($e->getMessage());
    });

The `setErrorHandler` method allows any callable type, for example:

.. code-block:: php

    <?php
    class JsonErrorHandler
    {
        public function __invoke(Request $request, Response $response, Exception $e) {
            $response->setStatusCode(500);
            $response->setContent(json_encode(
                [
                    "error" => [
                        "message" => $e->getMessage(),
                        "stacktrace" => $e->getTraceAsString(),
                    ],
                ]
            );
        }
    }

That's it!
