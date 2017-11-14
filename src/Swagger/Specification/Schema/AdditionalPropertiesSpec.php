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

use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecDecorator;

class AdditionalPropertiesSpec implements SpecDecorator
{
    protected $type;

    /**
     * ItemSpec constructor.
     *
     * @param string|SchemaSpec|ModelSpec $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @inheritDoc
     */
    public function getDecorator(): callable
    {
        return function (Schema $spec) {
            $schema = new Schema();
            if ($this->type instanceof SchemaSpec || $this->type instanceof ModelSpec) {
                $decorator = $this->type->getDecorator();
                $decorator($schema);
                $schema = $schema->getSchema();
            } else {
                $schema->setType($this->type);
            }

            $spec->setAdditionalProperties($schema);
        };
    }
}