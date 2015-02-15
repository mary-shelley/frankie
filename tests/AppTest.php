<?php
namespace Corley\Middleware;

use DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Corley\Demo\Controller\Index;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationReader;
use Corley\Middleware\Reader\HookReader as Reader;
use Corley\Middleware\Annotations\After;
use Acclimate\Container\CompositeContainer;
use DI\ContainerBuilder;
use Corley\Middleware\Loader\RouteAnnotationClassLoader;
use Symfony\Component\Routing\RequestContext;
use Corley\Middleware\Reader\HookReader;
use Corley\Middleware\Executor\AnnotExecutor;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Corley\Middleware\App;

class AppTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function setUp()
    {
        $loader = include __DIR__.'/../vendor/autoload.php';
        AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

        $container = new CompositeContainer();

        $builder = new ContainerBuilder();
        $builder->wrapContainer($container);
        $diContainer = $builder->build();
        $container->addContainer($diContainer);

        $reader = new AnnotationReader();

        $routeLoader = new RouteAnnotationClassLoader($reader);
        $loader = new AnnotationDirectoryLoader(new FileLocator([__DIR__.'/../app']), $routeLoader);
        $routes = $loader->load(__DIR__.'/../app');

        $context = new RequestContext();
        $matcher = new UrlMatcher($routes, $context);

        $hookReader = new HookReader($reader);

        $executor = new AnnotExecutor($container, $hookReader);

        $this->app = new App($matcher, $executor);
    }

    public function testSimpleCorrectFlow()
    {
        $request = Request::create("/");
        $response = new Response();
        $this->app->run($request, $response);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test404ErrorPage()
    {
        $request = Request::create("/neverland");
        $response = new Response();
        $this->app->run($request, $response);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testBeforeHookIsCalled()
    {
        $request = Request::create("/");
        $response = new Response();
        $this->app->run($request, $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"test": "Ok"}', $response->getContent());
    }

    public function testBeforeHookIsACallChain()
    {
        $request = Request::create("/far");
        $response = new Response();
        $this->app->run($request, $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"test": "Ok"}', $response->getContent());
    }

    public function testBeforeHookIsACallChainOverClasses()
    {
        $request = Request::create("/act");
        $response = new Response();
        $this->app->run($request, $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"ok":"json"}', $response->getContent());
    }

    public function testParametersArePassedToMethod()
    {
        $request = Request::create("/param/325/param/327");
        $response = new Response();
        $this->app->run($request, $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"one": 325, "two": 327}', $response->getContent());
    }
}
