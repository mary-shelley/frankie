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

/**
 * @requires PHP 5.6
 */
class App56Test extends \PHPUnit_Framework_TestCase
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
        $loader = new AnnotationDirectoryLoader(new FileLocator([__DIR__.'/app56']), $routeLoader);
        $routes = $loader->load(__DIR__.'/app56');

        $context = new RequestContext();
        $matcher = new UrlMatcher($routes, $context);

        $hookReader = new HookReader($reader);

        $executor = new AnnotExecutor($container, $hookReader);

        $this->app = new App($matcher, $executor);
    }

    public function testVariadicArguments()
    {
        $request  = Request::create("/user/1/company/2/support/ok/status");
        $response = new Response();

        ob_start();
        $this->app->run($request, $response);
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('["1","2","ok"]', $content);
    }
}
