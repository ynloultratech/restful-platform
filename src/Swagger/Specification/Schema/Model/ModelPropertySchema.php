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

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model;

class ModelPropertySchema
{
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_NUMBER = 'number';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_ARRAY = 'array';

    const FORMAT_INT32 = 'int32';
    const FORMAT_INT64 = 'int64';
    const FORMAT_FLOAT = 'float';
    const FORMAT_DOUBLE = 'double';
    const FORMAT_BYTE = 'byte'; //base64 encoded characters
    const FORMAT_BINARY = 'binary'; //any sequence of octets
    const FORMAT_DATE = 'date';  //As defined by full-date - RFC3339
    const FORMAT_DATETIME = 'date-time';  //As defined by full-date - RFC3339
    const FORMAT_PASSWORD = 'password';  //Used to hint UIs the input needs to be obscured.

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $format;

    /**
     * @var array
     */
    protected $enum = [];

    /**
     * Used when type is array and use associative array
     *
     * @var string|null
     */
    protected $keyType;

    /**
     * Used when type is array
     *
     * @var string|null
     */
    protected $itemType;

    /**
     * @var mixed
     */
    protected $example;

    /**
     * @var boolean
     */
    protected $required = false;

    /**
     * @var boolean
     */
    protected $readOnly = false;

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * ModelPropertySchema constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return ModelPropertySchema
     */
    public function setName(string $name): ModelPropertySchema
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ModelPropertySchema
     */
    public function setDescription(string $description = null): ModelPropertySchema
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return ModelPropertySchema
     */
    public function setType(string $type = null): ModelPropertySchema
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * @param string $itemType
     *
     * @return ModelPropertySchema
     */
    public function setItemType(string $itemType = null): ModelPropertySchema
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKeyType()
    {
        return $this->keyType;
    }

    /**
     * @param string $keyType
     *
     * @return ModelPropertySchema
     */
    public function setKeyType(string $keyType = null): ModelPropertySchema
    {
        $this->keyType = $keyType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return ModelPropertySchema
     */
    public function setFormat(string $format = null): ModelPropertySchema
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return array
     */
    public function getEnum(): array
    {
        return $this->enum;
    }

    /**
     * @param array $enum
     *
     * @return ModelPropertySchema
     */
    public function setEnum(array $enum): ModelPropertySchema
    {
        $this->enum = $enum;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExample()
    {
        return $this->example;
    }

    /**
     * @param mixed $example
     *
     * @return ModelPropertySchema
     */
    public function setExample($example = null): ModelPropertySchema
    {
        $this->example = $example;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return ModelPropertySchema
     */
    public function setRequired(bool $required): ModelPropertySchema
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * @param bool $readOnly
     *
     * @return ModelPropertySchema
     */
    public function setReadOnly(bool $readOnly): ModelPropertySchema
    {
        $this->readOnly = $readOnly;

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
     * @return ModelPropertySchema
     */
    public function setGroups(array $groups): ModelPropertySchema
    {
        $this->groups = $groups;

        return $this;
    }
}