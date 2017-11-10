<?php

namespace Tests\Error;

use Ynlo\RestfulPlatformBundle\Error\Error;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    public function testConstructor()
    {
        $error = new Error(200, 'error');
        self::assertEquals(200, $error->getCode());
        self::assertEquals('error', $error->getMessage());
    }
}
