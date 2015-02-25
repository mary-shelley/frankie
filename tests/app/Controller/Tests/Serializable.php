<?php
namespace Corley\Demo\Controller\Tests;

use Corley\Middleware\Annotations\After;
use Corley\Middleware\Annotations\Before;

/**
 * @After(targetClass="Corley\Demo\Controller\Tests\One", targetMethod="methodC")
 */
interface Serializable { }

