<?php

namespace Tests\Swagger\Model;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testCode()
    {
        $value = 200;
        self::assertEquals($value, (new Response($value))->getCode());
        self::assertEquals($value, (new Response(null))->setCode($value)->getCode());
    }
}
