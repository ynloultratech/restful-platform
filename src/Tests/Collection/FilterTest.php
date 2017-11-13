<?php

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
