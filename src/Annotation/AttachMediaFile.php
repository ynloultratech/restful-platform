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
 * @Annotation()
 * @Target("PROPERTY")
 */
class AttachMediaFile
{
    /**
     * @var string
     */
    public $storage;
}