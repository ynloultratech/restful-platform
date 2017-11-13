<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema;

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionSpec;
use PHPUnit\Framework\TestCase;

class DescriptionSpecTest extends TestCase
{
    public function testDecorator()
    {
        $value = 'description';
        $decorator = (new DescriptionSpec($value))->getDecorator();

        $descriptionAware = new class implements DescriptionAwareInterface
        {
            use DescriptionAwareTrait;
        };

        $decorator($descriptionAware);

        self::assertEquals($value, $descriptionAware->getDescription());
    }
}
