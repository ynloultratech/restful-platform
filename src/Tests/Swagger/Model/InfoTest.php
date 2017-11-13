<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Model;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Info;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    public function testVersion()
    {
        $value = 'v1.0';
        self::assertEquals($value, (new Info())->setVersion($value)->getVersion());
    }
}
