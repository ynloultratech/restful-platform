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

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ReferenceAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ReferenceAwareTrait;
use PHPUnit\Framework\TestCase;

class ReferenceAwareTraitTest extends TestCase
{
    public function testDecorator()
    {
        $refAware = new class implements ReferenceAwareInterface
        {
            use ReferenceAwareTrait;
        };
        $value = 'ref';
        self::assertEquals($value, $refAware->setRef($value)->getRef());
    }
}
