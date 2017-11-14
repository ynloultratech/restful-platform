<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema;

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaSpec;
use PHPUnit\Framework\TestCase;

class SchemaSpecTest extends TestCase
{
    public function testDecorator()
    {
        $decorator = (new SchemaSpec('User', [new DescriptionSpec('description')]))->getDecorator();
        $schemaAware = new class implements SchemaAwareInterface
        {
            use SchemaAwareTrait;
        };

        $decorator($schemaAware);

        //self::assertEquals('User', $schemaAware->getSchema()->getName());
        self::assertEquals('description', $schemaAware->getSchema()->getDescription());
    }
}
