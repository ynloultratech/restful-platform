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
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\AdditionalPropertiesSpec;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ModelSpec;

class AdditionalPropertiesSpecTest extends TestCase
{

    public function testDecorator()
    {
        $decorator = (new AdditionalPropertiesSpec('int'))->getDecorator();
        $schema = new Schema();

        $decorator($schema);

        self::assertEquals('int', $schema->getAdditionalProperties()->getType());
    }

    public function testDecoratorWithSchema()
    {
        $decorator = (new AdditionalPropertiesSpec(new ModelSpec(User::class, ['public'])))->getDecorator();
        $schema = new Schema();

        $decorator($schema);

        self::assertNotNull( $schema->getAdditionalProperties()->getProperties()->get('firstName'));
        self::assertNotNull( $schema->getAdditionalProperties()->getProperties()->get('lastName'));
        self::assertNotNull( $schema->getAdditionalProperties()->getProperties()->get('lastName'));
    }
}
