<?php
namespace Corley\Demo\Controller;

use Corley\Middleware\Annotations\Before;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Before(targetClass="Corley\Demo\Controller\Index", targetMethod="far")
 */
class My
{
    /**
     * @Route("/act")
     */
    public function act()
    {

    }
}
