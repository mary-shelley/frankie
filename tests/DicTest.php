<?php
namespace Corley\Middleware;

use DI\ContainerBuilder;

class MyClass {}
class OtherClass {
    /**
     * @Inject
     * @var Corley\Middleware\MyClass
     */
    public $myClass;
}

class DicTest extends \PHPUnit_Framework_TestCase
{
    private $builder;

    public function setUp()
    {
        $this->builder = new ContainerBuilder();
    }

    public function testInjectWithAnnotations()
    {
        $this->builder->useAutowiring(true);
        $this->builder->useAnnotations(true);

        $container = $this->builder->build();

        $otherClass = $container->get("Corley\\Middleware\\OtherClass");

        $this->assertInstanceOf("Corley\\Middleware\\OtherClass", $otherClass);
        $this->assertInstanceOf("Corley\\Middleware\\MyClass", $otherClass->myClass);
    }
}
