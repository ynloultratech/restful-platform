<?php
/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Model;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ExampleAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ExampleAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\NameAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\NameAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeAwareTrait;

class Schema implements SwaggerSpecModel,
    NameAwareInterface,
    DescriptionAwareInterface,
    SchemaAwareInterface,
    TypeAwareInterface,
    ExampleAwareInterface
{
    use NameAwareTrait;
    use DescriptionAwareTrait;
    use SchemaAwareTrait;
    use TypeAwareTrait;
    use ExampleAwareTrait;

    /**
     * @Serializer\SerializedName("title")
     * @var string
     */
    protected $name;

    /**
     * Mapped internal class
     *
     * @var string
     * @Serializer\Exclude()
     */
    protected $class;

    /**
     * @var Schema[]|ArrayCollection
     * @Serializer\Exclude(if="object.getProperties().isEmpty()")
     */
    protected $properties;

    /**
     * @var Schema
     */
    protected $items;

    /**
     * @var Schema
     * @Serializer\SerializedName("additionalProperties")
     */
    protected $additionalProperties;

    /**
     * @var Schema
     * @Serializer\Inline()
     */
    protected $schema;

    /**
     * @var array
     * @Serializer\Exclude()
     */
    protected $groups = [];

    /**
     * Schema constructor.
     *
     * @param $name
     */
    public function __construct($name = null)
    {
        //$this->name = $name;
        $this->properties = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|Schema[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param string $name
     *
     * @return Schema
     */
    public function getProperty($name)
    {
        return $this->properties->get($name);
    }

    /**
     * @param ArrayCollection|Schema[] $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return Schema
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param Schema $items
     */
    public function setItems(Schema $items)
    {
        $this->items = $items;
    }

    /**
     * @return Schema
     */
    public function getAdditionalProperties(): Schema
    {
        if (!$this->additionalProperties) {
            $this->additionalProperties = new Schema();
        }

        return $this->additionalProperties;
    }

    /**
     * @param Schema $additionalProperties
     */
    public function setAdditionalProperties(Schema $additionalProperties)
    {
        $this->additionalProperties = $additionalProperties;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
    }
}