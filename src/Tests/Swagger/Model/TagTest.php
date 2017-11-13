<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Model;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testConstructor()
    {
        $tag = new Tag('name', 'description');
        self::assertEquals('name', $tag->getName());
        self::assertEquals('description', $tag->getDescription());
    }
}
