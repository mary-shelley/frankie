<?php
namespace Corley\Demo\Controller;

use Corley\Middleware\Annotations\After;
use Corley\Middleware\Annotations\Before;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Before(targetClass="Corley\Demo\Controller\Index", targetMethod="far")
 * @After(targetClass="Corley\Demo\Controller\Index", targetMethod="toJson")
 */
class My
{
    /**
     * @Route("/act")
     */
    public function act()
    {
        return ["ok" => "json"];
    }
}
