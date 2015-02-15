<?php
namespace Corley\Middleware\Stub;

use Symfony\Component\Routing\Annotation\Route;

class Sut
{
    /**
     * @Route("/a-path", methods={"GET"})
     */
    public function anAction()
    {
    }
}
