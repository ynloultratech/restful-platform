<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Root;

use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Path\OperationSpec;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Root\PathSpec;

class PathSpecTest extends TestCase
{
    public function testDecorator()
    {
        $path = (new PathSpec('/admin/users', [new OperationSpec('post', [])]))->getDecorator();
        $swObject = new SwaggerObject();
        $path($swObject);

        self::assertNotNull($swObject->getPath('/admin/users')->getOperations()->get('post'));
    }
}
