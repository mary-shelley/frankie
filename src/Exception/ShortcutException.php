<?php
namespace Corley\Middleware\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ShortcutException extends Exception
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
