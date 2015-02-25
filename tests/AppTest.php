<?php
namespace Corley\Middleware;

use DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationReader;
use Corley\Middleware\Reader\HookReader as Reader;
use Acclimate\Container\CompositeContainer;
use DI\ContainerBuilder;
use Corley\Middleware\Loader\RouteAnnotationClassLoader;
use Symfony\Component\Routing\RequestContext;
use Corley\Middleware\Reader\HookReader;
use Corley\Middleware\Executor\AnnotExecutor;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class AppTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function setUp()
    {
        $loader = include __DIR__.'/../vendor/autoload.php';
        AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

        $container = new CompositeContainer();

        $builder = new ContainerBuilder();
        $builder->useAnnotations(true);
        $builder->wrapContainer($container);
        $diContainer = $builder->build();
        $container->addContainer($diContainer);

        $reader = new AnnotationReader();

        $routeLoader = new RouteAnnotationClassLoader($reader);
        $loader = new AnnotationDirectoryLoader(new FileLocator([__DIR__.'/app']), $routeLoader);
        $routes = $loader->load(__DIR__.'/app');

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

    public function testEventsFlow()
    {
        $request = Request::create("/base-flow");
        $response = new Response();

        ob_start();
        $this->app->run($request, $response);
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(<<<EOF
Corley\\Demo\\Controller\\Tests\\Two::methodB
Corley\\Demo\\Controller\\Tests\\One::methodC
Corley\\Demo\\Controller\\Tests\\One::action

EOF
        ,$content);
    }

    public function testAfterEventsFlow()
    {
        $request = Request::create("/after-flow");
        $response = new Response();

        ob_start();
        $this->app->run($request, $response);
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(<<<EOF
Corley\\Demo\\Controller\\Tests\\Three::action
Corley\\Demo\\Controller\\Tests\\Three::methodC
Corley\\Demo\\Controller\\Tests\\Four::methodB

EOF
        ,$content);
    }

    public function testCicleEventsFlow()
    {
        $request = Request::create("/flow");
        $response = new Response();

        ob_start();
        $this->app->run($request, $response);
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(<<<EOF
Corley\\Demo\\Controller\\Tests\\Two::methodB
Corley\\Demo\\Controller\\Tests\\One::methodC
Corley\\Demo\\Controller\\Tests\\One::action
Corley\\Demo\\Controller\\Tests\\Five::action
Corley\\Demo\\Controller\\Tests\\Three::action
Corley\\Demo\\Controller\\Tests\\Three::methodC
Corley\\Demo\\Controller\\Tests\\Four::methodB

EOF
        ,$content);
    }

    public function testInterfaceEngagement()
    {
        $request = Request::create("/from-interface");
        $response = new Response();

        ob_start();
        $this->app->run($request, $response);
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(<<<EOF
Corley\\Demo\\Controller\\Tests\\Two::methodB
Corley\\Demo\\Controller\\Tests\\Eight::action
Corley\\Demo\\Controller\\Tests\\One::methodC

EOF
        ,$content);
    }
    public function testEngagementClassesAndInterfaces()
    {
        $request = Request::create("/long-with-interface");
        $response = new Response();

        ob_start();
        $this->app->run($request, $response);
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(<<<EOF
Corley\\Demo\\Controller\\Tests\\Two::methodB
Corley\\Demo\\Controller\\Tests\\Four::methodB
Corley\\Demo\\Controller\\Tests\\Nine::action
Corley\\Demo\\Controller\\Tests\\Four::methodB
Corley\\Demo\\Controller\\Tests\\One::methodC

EOF
        ,$content);
    }

    public function testExceptionHandling()
    {
        $request = Request::create("/nowhere");
        $response = new Response();

        $count = 0;
        $this->app->setErrorHandler(function() use (&$count) {
            $count++;
        });
        $this->app->run($request, $response);

        $this->assertSame(1, $count);
    }

    public function testFlowShortCircuitOnResponse()
    {
        $request = Request::create("/deny");
        $response = new Response();

        ob_start();
        $this->app->run($request, $response);
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals("", $content);
    }

    public function testShortCircuitOnActions()
    {
        $request = Request::create("/close-direct");
        $response = new Response();

        ob_start();
        $this->app->run($request, $response);
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertEquals(<<<EOF
Corley\\Demo\\Controller\\Tests\\Seven::action

EOF
, $content);
    }

    public function testShortCircuitDuringAfterSteps()
    {
        $request = Request::create("/close-after");
        $response = new Response();

        ob_start();
        $this->app->run($request, $response);
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(<<<EOF
Corley\\Demo\\Controller\\Tests\\Seven::pass

EOF
, $content);
    }
}
