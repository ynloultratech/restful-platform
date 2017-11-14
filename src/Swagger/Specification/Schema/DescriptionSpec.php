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

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\ValueSpec;

class DescriptionSpec extends ValueSpec
{
    /**
     * @param DescriptionAwareInterface $spec
     * @param string                    $value
     */
    public function setValue($spec, $value)
    {
        $spec->setDescription($value);
    }
}