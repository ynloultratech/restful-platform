<?php

namespace Tests\Swagger\Specification\Operation;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation\TagSpec;
use PHPUnit\Framework\TestCase;

class TagSpecTest extends TestCase
{
    public function testDecorator()
    {
        $tag1 = (new TagSpec('books'))->getDecorator();
        $tag2 = (new TagSpec('backend'))->getDecorator();

        $operation = new Operation();

        $tag1($operation);
        $tag2($operation);

        self::assertEquals(2, $operation->getTags()->count());
        self::assertEquals('books', $operation->getTags()->first());
        self::assertEquals('backend', $operation->getTags()->last());
    }
}
