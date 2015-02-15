<?php
namespace Corley\Middleware\Reader;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Corley\Middleware\Annotations\Before;
use Corley\Middleware\Annotations\After;

class HookReaderTest extends \PHPUnit_Framework_TestCase
{
    private $sut;

    public function setUp()
    {
        $loader = require __DIR__.'/../../vendor/autoload.php';
        AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

        $this->sut = new HookReader(new AnnotationReader());
    }

    public function testFilterBeforeMethodSteps()
    {
        $annots = $this->sut->getMethodAnnotationsFor("Corley\\Demo\\Controller\\Index", "index", Before::class);

        $this->assertCount(1, $annots);
    }

    public function testFilterBeforeClassSteps()
    {
        $annots = $this->sut->getClassAnnotationsFor("Corley\\Demo\\Controller\\My", Before::class);

        $this->assertCount(1, $annots);
    }

    public function testFilterAfterMethodSteps()
    {
        $annots = $this->sut->getMethodAnnotationsFor("Corley\\Demo\\Controller\\Index", "step", After::class);

        $this->assertCount(1, $annots);
    }

    public function testFilterAfterClassSteps()
    {
        $annots = $this->sut->getClassAnnotationsFor("Corley\\Demo\\Controller\\My", After::class);

        $this->assertCount(1, $annots);
    }
}
