<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Annotation;

/**
 * A model definition is a way to display or/and collect user data
 * Definition use a serializer groups to hide/show properties
 *
 * @Annotation()
 * @Target("CLASS")
 */
final class ModelDefinition
{
    /**
     * Unique name to identify the definition
     *
     * NOTE: this name should be unique in the entire app
     *
     * @var string
     */
    public $name;

    /**
     * Serializer groups to use when use this definition
     *
     * @var array
     */
    public $serializerGroups = [];

    /**
     * List of operations when this definition will be loaded automatically
     *
     * @var array
     */
    public $operations = [];

    /**
     * Class of the API end to restrict operations
     * this is helpful when has many endpoint for same class, e.g. multi-kernel app
     *
     * @var string
     */
    public $endpoint;

    /**
     *
     * @var string
     */
    public $direction = 'any'; //can be `in` or `out` to apply the definition only for serialization or deserialization
}
