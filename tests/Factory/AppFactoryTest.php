<?php
namespace Corley\Middleware\Factory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testAppCreation()
    {
        $this->markTestSkipped("need a refactor");

        $container = $this->prophesize("Acclimate\Container\CompositeContainer");
        $request = Request::create("/");
        $response = new Response();

        $app = AppFactory::createApp(__DIR__, $container->reveal(), $request, $response);

        $this->assertInstanceOf("Corley\\Middleware\\App", $app);
    }
}
