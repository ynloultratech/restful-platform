<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Collection;

use Ynlo\RestfulPlatformBundle\Collection\Filter;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    public function testFilter()
    {
        $filter = new Filter('param', 'field', 'type', 'description', 'example');
        self::assertEquals('param', $filter->getParameter());
        self::assertEquals('field', $filter->getField());
        self::assertEquals('type', $filter->getType());
        self::assertEquals('description', $filter->getDescription());
        self::assertEquals('example', $filter->getExample());

        self::assertEquals('param1', $filter->setParameter('param1')->getParameter());
        self::assertEquals('field1', $filter->setField('field1')->getField());
        self::assertEquals('type1', $filter->setType('type1')->getType());
        self::assertEquals('description1', $filter->setDescription('description1')->getDescription());
        self::assertEquals('example1', $filter->setExample('example1')->getExample());
    }
}
