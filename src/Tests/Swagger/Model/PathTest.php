<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Path;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function testConstructor()
    {
        $value = '/admin';
        $path = new Path($value);
        self::assertEquals($value, $path->getPath());
        self::assertNotNull($path->getOperations());
    }

    public function testOperations()
    {
        $value = new ArrayCollection();
        self::assertEquals($value, (new Path(''))->setOperations($value)->getOperations());
    }
}
