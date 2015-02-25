<?php
namespace Corley\Demo\Controller\Tests;

use Corley\Middleware\Annotations\After;
use Corley\Middleware\Annotations\Before;

/**
 * @Before(targetClass="Corley\Demo\Controller\Tests\Two", targetMethod="methodB")
 */
interface Authenticable { }
