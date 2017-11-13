<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\PropertySpec;
use PHPUnit\Framework\TestCase;

class PropertySpecTest extends TestCase
{
    public function testDecorator()
    {
        $decorator = (new PropertySpec('username', [new DescriptionSpec('description')]))->getDecorator();
        $schema = new Schema();
        $decorator($schema);

        self::assertEquals('description', $schema->getProperty('username')->getDescription());
    }
}
