<?php
use DI\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Corley\Middleware\App;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Config\FileLocator;
use Corley\Middleware\Loader\RouteAnnotationClassLoader;
use Corley\Middleware\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;

$loader = require_once __DIR__.'/../vendor/autoload.php';
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$builder = new ContainerBuilder();
$container = $builder->build();

$request = Request::createFromGlobals();
$response = new Response();

$reader = new AnnotationReader();

$routeLoader = new RouteAnnotationClassLoader($reader);
$loader = new AnnotationDirectoryLoader(new FileLocator([__DIR__.'/../app']), $routeLoader);
$routes = $loader->load(__DIR__.'/../app');

$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

$hookReader = new Reader($reader);

$app = new App($container, $hookReader);
$app->setRouter($matcher);
$response = $app->run($request, $response);
$response->send();
