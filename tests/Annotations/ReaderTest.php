<?php
namespace Corley\Middleware\Annotations;

use ReflectionMethod;
use ReflectionClass;
use Corley\Middleware\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    private $sut;

    public function setUp()
    {
        $loader = require __DIR__ . '/../../vendor/autoload.php';
        AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

        $this->sut = new Reader();
    }

    public function testFilterBeforeMethodSteps()
    {
        $refl = new ReflectionMethod("Corley\\Demo\\Controller\\Index", "index");
        $annots = $this->sut->getBeforeMethodAnnotations($refl);

        $this->assertCount(1, $annots);
    }

    public function testFilterBeforeClassSteps()
    {
        $refl = new ReflectionClass("Corley\\Demo\\Controller\\My");
        $annots = $this->sut->getBeforeClassAnnotations($refl);

        $this->assertCount(1, $annots);
    }
}
