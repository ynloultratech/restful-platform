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

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\ValueSpec;

/**
 * Usage:
 *
 * TypeSpec('string')
 *
 * TypeSpec(['string', 'date-time'])
 */
class TypeSpec extends ValueSpec
{
    /**
     * @param TypeAwareInterface $spec
     * @param string             $value
     */
    public function setValue($spec, $value)
    {
        if (is_array($value)) {
            list($type, $format) = $value;
            $spec->setType($type);
            if ($format) {
                $spec->setFormat($format);
            }
        } else {
            $spec->setType($value);
        }
    }
}