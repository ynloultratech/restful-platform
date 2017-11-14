<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model;

use JMS\Serializer\Metadata\VirtualPropertyMetadata;

class DescribeContext
{
    /**
     * @var \ReflectionProperty|\ReflectionClass|VirtualPropertyMetadata
     */
    protected $property;

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * DescribeContext constructor.
     *
     * @param \ReflectionClass|\ReflectionProperty|VirtualPropertyMetadata $property
     * @param array                                                        $groups
     */
    public function __construct($property, array $groups = [])
    {
        $this->property = $property;
        $this->groups = $groups;
    }

    /**
     * @return \ReflectionClass|\ReflectionProperty|VirtualPropertyMetadata
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}