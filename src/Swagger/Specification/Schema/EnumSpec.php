<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Property;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\ValueSpec;

class EnumSpec extends ValueSpec
{
    /**
     * @param Property $spec
     * @param array    $value
     */
    public function setValue($spec, $value)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('The value should be a array');
        }

        if (!$spec instanceof Property) {
            throw new \InvalidArgumentException(sprintf('Enum only is applicable for "%s"', Property::class));
        }

        $spec->setEnum($value);
    }
}