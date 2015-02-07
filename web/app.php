<?php
use DI\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Corley\Middleware\App;

$loader = require_once __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$builder = new ContainerBuilder();
$container = $builder->build();

$app = new App($container);
$app->setRequest(Request::createFromGlobals());
$app->setResponse(new Response());
$app->run(__DIR__ . '/../app');
