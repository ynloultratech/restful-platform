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

interface ModelDescriberInterface
{
    /**
     * Describe the given property based on information extracted from context
     * Each describer can add helpful information to property schema
     *
     * @param ModelPropertySchema $property
     * @param DescribeContext     $context
     *
     * @return mixed
     */
    public function describe(ModelPropertySchema $property, DescribeContext $context);

    /**
     * Can describe this property?
     *
     * @param ModelPropertySchema $property
     * @param DescribeContext     $context
     *
     * @return mixed
     */
    public function supports(ModelPropertySchema $property, DescribeContext $context);
}