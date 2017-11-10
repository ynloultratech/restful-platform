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

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema;

interface TypeAwareInterface
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
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType(string $type = null);

    /**
     * @return string
     */
    public function getFormat();

    /**
     * @param string $format
     */
    public function setFormat(string $format = null);
}