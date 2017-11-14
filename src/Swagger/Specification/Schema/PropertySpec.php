<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Property;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecContainer;

class PropertySpec extends SpecContainer
{
    protected $name;

    /**
     * PropertySpec constructor.
     *
     * @param string $name
     * @param array  $spec
     */
    public function __construct($name, array $spec = [])
    {
        $this->name = $name;
        parent::__construct($spec);
    }

    /**
     * @inheritDoc
     */
    public function getDecorator(): callable
    {
        return function (Schema $schema) {
            $property = new Property();
            $propertyDecorator = parent::getDecorator();
            $propertyDecorator($property);

            $schema->getProperties()->set($this->name, $property);
        };
    }
}