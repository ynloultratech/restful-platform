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

use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ItemSpec;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ModelSpec;

class ItemSpecTest extends TestCase
{
    public function testDecorator()
    {
        $decorator = (new ItemSpec('string', [new DescriptionSpec('description')]))->getDecorator();
        $schema = new Schema();
        $decorator($schema);

        self::assertEquals('string', $schema->getItems()->getType());
        self::assertEquals('description', $schema->getItems()->getDescription());
    }

    public function testDecoratorWithModel()
    {
        $decorator = (new ItemSpec(new ModelSpec(User::class, ['public'])))->getDecorator();
        $schema = new Schema();
        $decorator($schema);

        self::assertNull($schema->getItems()->getProperty('username'));
        self::assertNotNull($schema->getItems()->getProperty('firstName'));
        self::assertNotNull($schema->getItems()->getProperty('lastName'));
    }
}
