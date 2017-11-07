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

namespace Ynlo\RestfulPlatformBundle\Collection;

class Filter
{

    /**
     * @var string
     */
    protected $parameter;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string|null
     */
    protected $example;

    /**
     * Filter constructor.
     *
     * @param string      $parameter
     * @param string      $field
     * @param null|string $type
     * @param null|string $description
     * @param null|string $example
     */
    public function __construct($parameter, $field, $type = 'string', $description = null, $example = null)
    {
        $this->parameter = $parameter;
        $this->field = $field;
        $this->type = $type;
        $this->description = $description;
        $this->example = $example;
    }

    /**
     * @return string
     */
    public function getParameter(): string
    {
        return $this->parameter;
    }

    /**
     * @param string $parameter
     */
    public function setParameter(string $parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * @return string|callable
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField(string $field)
    {
        $this->field = $field;
    }

    /**
     * @return null|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return null|string
     */
    public function getExample()
    {
        return $this->example;
    }

    /**
     * @param null|string $example
     */
    public function setExample($example)
    {
        $this->example = $example;
    }
}