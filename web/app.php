<?php
use DI\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Corley\Middleware\App;
use Acclimate\Container\CompositeContainer;
use Corley\Middleware\Factory\AppFactory;

$loader = require_once __DIR__.'/../vendor/autoload.php';
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$container = new CompositeContainer();

$builder = new ContainerBuilder();
$builder->setDefinitionCache(new Doctrine\Common\Cache\ApcCache());
$diContainer = $builder->build();

$container->addContainer($diContainer);

$request = Request::createFromGlobals();
$response = new Response();

AppFactory::$DEBUG = false;
AppFactory::$CACHE_FOLDER = "/tmp";
$app = AppFactory::createApp(__DIR__.'/../app', $container, $request, $response);
$response = $app->run($request, $response);
$response->send();
