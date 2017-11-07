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

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ExampleAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ExampleAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\NameAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\NameAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeAwareTrait;
use JMS\Serializer\Annotation as Serializer;

class Parameter implements
    SwaggerSpecModel,
    DescriptionAwareInterface,
    NameAwareInterface,
    TypeAwareInterface,
    SchemaAwareInterface,
    ExampleAwareInterface
{
    use DescriptionAwareTrait;
    use NameAwareTrait;
    use TypeAwareTrait;
    use SchemaAwareTrait;
    use ExampleAwareTrait;

    const IN_PATH = 'path';
    const IN_HEADER = 'header';
    const IN_QUERY = 'query';
    const IN_FORM = 'formData';
    const IN_BODY = 'body';

    /**
     * @var string
     * @Serializer\SerializedName("in")
     */
    protected $in;

    /**
     * @var boolean
     */
    protected $required = false;


    /**
     * @param string $name
     * @param string $in
     */
    public function __construct($name, $in = self::IN_QUERY)
    {
        $this->name = $name;
        $this->in = $in;

        if (in_array($in, [self::IN_PATH, self::IN_BODY])) {
            $this->required = true;
        }
    }

    /**
     * @return string
     */
    public function getIn(): string
    {
        return $this->in;
    }

    /**
     * @param string $in
     */
    public function setIn(string $in)
    {
        $this->in = $in;
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
     */
    public function setRequired(bool $required)
    {
        $this->required = $required;
    }
}