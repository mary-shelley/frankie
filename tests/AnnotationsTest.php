<?php
namespace Corley\Middleware;

use ReflectionClass;
use ReflectionMethod;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

use Corley\Middleware\Annotations as Corley;

/**
 * @Corley\Pre(targetClass="AnotherClass", targetMethod="hello")
 */
class Sut
{
    /**
     * @Corley\Pre(targetClass="SuperClass", targetMethod="aMethod")
     */
    public function method()
    {

    }
}

class AnnotationsTest extends \PHPUnit_Framework_TestCase
{
    private $reader;

    public function setUp()
    {
        AnnotationRegistry::registerFile(__DIR__ . "/../src/Annotations/Pre.php");
        $this->reader = new AnnotationReader();
    }

    public function testReadClassAnnotations()
    {
        $reflClass = new ReflectionClass('Corley\\Middleware\\Sut');
        $annotations = $this->reader->getClassAnnotations($reflClass);

        $this->assertCount(1, $annotations);
        foreach ($annotations as $annot) {
            $this->assertInstanceOf("Corley\\Middleware\\Annotations\\Pre", $annot);

            $this->assertEquals("AnotherClass", $annot->targetClass);
            $this->assertEquals("hello", $annot->targetMethod);
        }
    }

    public function testReadMethodAnnotations()
    {
        $reflClass = new ReflectionMethod('Corley\\Middleware\\Sut', "method");
        $annotations = $this->reader->getMethodAnnotations($reflClass);

        $this->assertCount(1, $annotations);
        foreach ($annotations as $annot) {
            $this->assertInstanceOf("Corley\\Middleware\\Annotations\\Pre", $annot);

            $this->assertEquals("SuperClass", $annot->targetClass);
            $this->assertEquals("aMethod", $annot->targetMethod);
        }
    }
}
