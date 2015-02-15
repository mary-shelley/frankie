<?php
namespace Corley\Middleware;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    private $routes;

    public function setUp()
    {
        $routes = new RouteCollection();
        $routes->add('hello', new Route('/hello', array('_controller' => 'foo')));

        $this->routes = $routes;
    }

    public function testSimpleRoute()
    {
        $context = new RequestContext();

        $request = Request::create("/hello");
        $context->fromRequest($request);
        $matcher = new UrlMatcher($this->routes, $context);

        $parameters = $matcher->match('/hello');

        $this->assertJsonStringEqualsJsonString('{"_route": "hello", "_controller": "foo"}', json_encode($parameters));
    }
}
