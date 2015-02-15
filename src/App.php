<?php
namespace Corley\Middleware;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Corley\Middleware\Executor\AnnotExecutor;

class App
{
    private $router;
    private $executor;

    public function __construct(UrlMatcher $router, AnnotExecutor $executor)
    {
        $this->router = $router;
        $this->executor = $executor;
    }

    public function run(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        try {
            $matched = $this->getRouter()->matchRequest($request);
            $this->getExecutor()->execute($request, $response, $matched);
        } catch (ResourceNotFoundException $e) {
            $response->setStatusCode(404);
        } catch (Exception $e) {
            $response->setStatusCode(500);
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
}
