<?php
namespace Corley\Middleware;

use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Config\FileLocator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Corley\Middleware\Loader\RouteAnnotationClassLoader;

class RouteAnnotationTest extends \PHPUnit_Framework_TestCase
{
    private $annotClassLoader;

    public function setUp()
    {
        $loader = require __DIR__.'/../vendor/autoload.php';
        AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
        $reader = new AnnotationReader();

        $this->annotClassLoader = new RouteAnnotationClassLoader($reader);
    }

    public function testCollectRoutes()
    {
        $loader = new AnnotationDirectoryLoader(new FileLocator([__DIR__."/Stub"]), $this->annotClassLoader);
        $collections = $loader->load(__DIR__.'/Stub');

        $this->assertCount(1, $collections);
        $collections = $collections->all();
        $route = array_pop($collections);

        $this->assertEquals("Corley\\Middleware\\Stub\\Sut", $route->getDefaults()["controller"]);
    }
}
