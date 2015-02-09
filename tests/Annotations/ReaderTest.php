<?php
namespace Corley\Middleware\Annotations;

use ReflectionMethod;
use ReflectionClass;
use Corley\Middleware\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    private $sut;

    public function setUp()
    {
        $loader = require __DIR__ . '/../../vendor/autoload.php';
        AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

        $this->sut = new Reader(new AnnotationReader());
    }

    public function testFilterBeforeMethodSteps()
    {
        $annots = $this->sut->getBeforeMethodAnnotations("Corley\\Demo\\Controller\\Index", "index");

        $this->assertCount(1, $annots);
    }

    public function testFilterBeforeClassSteps()
    {
        $annots = $this->sut->getBeforeClassAnnotations("Corley\\Demo\\Controller\\My");

        $this->assertCount(1, $annots);
    }
}
