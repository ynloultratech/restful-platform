<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
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
     * @var ArrayCollection
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
     * @return ArrayCollection
     */
    public function getProperties(): ArrayCollection
    {
        return $this->properties;
    }

    /**
     * @param string $name
     *
     * @return Schema|null
     */
    public function getProperty($name)
    {
        return $this->properties->get($name);
    }

    /**
     * @param ArrayCollection|Schema[] $properties
     *
     * @return $this;
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @return Schema|null
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param Schema $items
     *
     * @return $this;
     */
    public function setItems(Schema $items)
    {
        $this->items = $items;

        return $this;
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
     *
     * @return $this;
     */
    public function setAdditionalProperties(Schema $additionalProperties)
    {
        $this->additionalProperties = $additionalProperties;

        return $this;
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
     *
     * @return $this;
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
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
     *
     * @return $this
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }
}