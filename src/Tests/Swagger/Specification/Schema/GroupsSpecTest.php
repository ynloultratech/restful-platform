<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\GroupsSpec;
use PHPUnit\Framework\TestCase;

class GroupsSpecTest extends TestCase
{
    public function testDecorator()
    {
        $value = ['admin', 'backend'];
        $decorator = (new GroupsSpec($value))->getDecorator();
        $schema = new Schema();
        $decorator($schema);

        self::assertEquals($value, $schema->getGroups());
    }
}
