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
 * Use this annotation to use a custom object constructor.
 *
 * NOTE: By default Serializer use Doctrine object constructor.
 *
 * @Annotation()
 * @Target("CLASS")
 */
class DeserializerObjectConstructor
{
    /**
     * @var string
     */
    public $service;
}