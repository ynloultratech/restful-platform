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

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer;

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelDescriberInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;

class ExampleDescriber implements ModelDescriberInterface
{
    /**
     * @inheritDoc
     */
    public function describe(ModelPropertySchema $property, DescribeContext $context)
    {
        if (!$property->getExample()) {
            $example = null;
            switch ($property->getType()) {
                case ModelPropertySchema::TYPE_INTEGER:
                case ModelPropertySchema::TYPE_NUMBER:
                    $example = 0;
                    break;
                case ModelPropertySchema::TYPE_STRING:
                    switch ($property->getFormat()) {
                        case ModelPropertySchema::FORMAT_DATETIME:
                            $example = (new \DateTime())->modify("00:00:00")->format('Y-m-d\TH:i:sO');
                            break;
                        case ModelPropertySchema::FORMAT_DATE:
                            $example = (new \DateTime())->modify("00:00:00")->format('Y-m-d');
                            break;
                        case ModelPropertySchema::FORMAT_DOUBLE:
                        case ModelPropertySchema::FORMAT_FLOAT:
                            $example = 0.00;
                            break;
                    }

                    break;
            }

            $property->setExample($example);
        }
    }

    /**
     * @inheritDoc
     */
    public function supports(ModelPropertySchema $property, DescribeContext $context)
    {
        return true;
    }
}