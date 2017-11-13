<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Root;

use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Root\InfoSpec;
use PHPUnit\Framework\TestCase;

class InfoSpecTest extends TestCase
{
    public function testDecorator()
    {
        $info = (new InfoSpec('Some API', 'Description', 'v1.0'))->getDecorator();
        $swObject = new SwaggerObject();
        $info($swObject);

        self::assertEquals('Some API', $swObject->getInfo()->getName());
        self::assertEquals('Description', $swObject->getInfo()->getDescription());
        self::assertEquals('v1.0', $swObject->getInfo()->getVersion());
    }
}
