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

use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecContainer;

class ItemSpec extends SpecContainer
{
    protected $type;

    /**
     * ItemSpec constructor.
     *
     * @param string|SchemaSpec|ModelSpec $type
     * @param                             $specs
     */
    public function __construct($type, array $specs = [])
    {
        $this->type = $type;

        if (is_string($type)) {
            $specs = array_merge([new TypeSpec($type)], $specs);
        }

        parent::__construct($specs);
    }

    /**
     * @inheritDoc
     */
    public function getDecorator(): callable
    {
        return function (Schema $spec) {
            $itemsSchema = new Schema();
            if ($this->type instanceof SchemaSpec || $this->type instanceof ModelSpec) {
                $decorator = $this->type->getDecorator();
                $decorator($itemsSchema);
                $itemsSchema = $itemsSchema->getSchema();
            } else {
                $schemaDecorator = parent::getDecorator();
                $schemaDecorator($itemsSchema);
            }

            $spec->setItems($itemsSchema);
        };
    }
}