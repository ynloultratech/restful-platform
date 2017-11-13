<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Collection;

use Ynlo\RestfulPlatformBundle\Collection\FilterCollection;
use PHPUnit\Framework\TestCase;

class FilterCollectionTest extends TestCase
{
    public function testFilterCollection()
    {
        $collection = new FilterCollection();
        $collection->addFilter('param1');

        self::assertEquals('param1', $collection->getFilters()['param1']->getField());

        $collection->addFilter('username', 'name', 'type', 'description', 'example');
        self::assertEquals('name', $collection->getFilters()['username']->getField());
        self::assertEquals('type', $collection->getFilters()['username']->getType());
        self::assertEquals('description', $collection->getFilters()['username']->getDescription());
        self::assertEquals('example', $collection->getFilters()['username']->getExample());
    }
}
