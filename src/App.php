<?php
namespace Corley\Middleware;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Corley\Middleware\Executor\AnnotExecutor;
use Symfony\Component\Routing\RequestContext;

class App
{
    private $router;
    private $executor;
    private $errorHandler;

    public function __construct(UrlMatcher $router, AnnotExecutor $executor)
    {
        $this->router = $router;
        $this->executor = $executor;
        $this->setErrorHandler(function(){});
    }

    public function run(Request $request, Response $response)
    {
        $requestContext = new RequestContext();
        $requestContext = $requestContext->fromRequest($request);
        $this->getRouter()->setContext($requestContext);

        try {
            $matched = $this->getRouter()->matchRequest($request);
            $response = $this->getExecutor()->execute($request, $response, $matched);
        } catch (ResourceNotFoundException $e) {
            $response->setStatusCode(404);
            call_user_func_array($this->errorHandler, [$request, $response, $e]);
        } catch (Exception $e) {
            $response->setStatusCode(500);
            call_user_func_array($this->errorHandler, [$request, $response, $e]);
        }

        return $response;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getExecutor()
    {
        return $this->executor;
    }

    public function setErrorHandler(callable $callable)
    {
        $this->errorHandler = $callable;
    }
}
