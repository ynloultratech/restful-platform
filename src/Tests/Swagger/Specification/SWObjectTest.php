<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification;

use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Path\OperationSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWObject;
use PHPUnit\Framework\TestCase;

class SWObjectTest extends TestCase
{
    public function testInfo()
    {
        $decorator = (SWObject::info('title', 'description', 'version'))->getDecorator();
        $swObject = new SwaggerObject();
        $decorator($swObject);
        self::assertEquals('title', $swObject->getInfo()->getName());
        self::assertEquals('description', $swObject->getInfo()->getDescription());
        self::assertEquals('version', $swObject->getInfo()->getVersion());
    }

    public function testBasePath()
    {
        $decorator = (SWObject::basePath('/admin'))->getDecorator();
        $swObject = new SwaggerObject();
        $decorator($swObject);
        self::assertEquals('/admin', $swObject->getBasePath());
    }

    public function testHost()
    {
        $decorator = (SWObject::host('example.com'))->getDecorator();
        $swObject = new SwaggerObject();
        $decorator($swObject);
        self::assertEquals('example.com', $swObject->getHost());
    }

    public function testPath()
    {
        $decorator = (SWObject::path('/users', [new OperationSpec('post', [])]))->getDecorator();
        $swObject = new SwaggerObject();
        $decorator($swObject);
        self::assertNotNull($swObject->getPath('/users')->getOperations()->get('post'));
    }
}
