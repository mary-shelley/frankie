<?php
namespace Corley\Middleware;

use DI\ContainerBuilder;

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

    public function testInjectOverTraits()
    {
        $container = $this->builder->build();
        $obj = $container->get("Corley\\MiddleWare\\OverTrait");

        $this->assertInstanceOf("Corley\\Middleware\\MyClass", $obj->getMyClass());
    }
}

// Support test classes...

class MyClass {}
class OtherClass {
    /**
     * @Inject
     * @var Corley\Middleware\MyClass
     */
    public $myClass;
}

trait Classable
{
    /**
     * @Inject
     * @var Corley\Middleware\MyClass
     */
    private $myClass;

    public function getMyClass()
    {
        return $this->myClass;
    }
}

class OverTrait
{
    use Classable;
}

