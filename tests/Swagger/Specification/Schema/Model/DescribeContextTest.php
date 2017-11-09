<?php

namespace Tests\Swagger\Specification\Schema\Model;

use Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use PHPUnit\Framework\TestCase;

class DescribeContextTest extends TestCase
{
    public function testConstructor()
    {
        $property = new \ReflectionProperty(User::class, 'username');
        $describeContext = new DescribeContext($property, ['public']);
        self::assertEquals($property, $describeContext->getProperty());
        self::assertEquals(['public'], $describeContext->getGroups());
    }
}
