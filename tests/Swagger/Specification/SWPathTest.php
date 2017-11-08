<?php

namespace Tests\Swagger\Specification;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Path;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWOperation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWPath;
use PHPUnit\Framework\TestCase;

class SWPathTest extends TestCase
{
    /**
     * @dataProvider methods
     */
    public function testMethods($method)
    {
        $decorator = (SWPath::$method([SWOperation::operationId('operation')]))->getDecorator();
        $path = new Path('/admin/users');
        $decorator($path);

        self::assertEquals('operation', $path->getOperations()->get($method)->getOperationId());
    }

    public function methods()
    {
        return [
            ['get'],
            ['post'],
            ['put'],
            ['delete'],
            ['patch'],
            ['options'],
            ['head'],
        ];
    }
}
