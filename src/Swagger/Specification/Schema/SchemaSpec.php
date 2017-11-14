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
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecContainer;

class SchemaSpec extends SpecContainer
{
    protected $name;

    /**
     * SchemaSpec constructor.
     *
     * @param       $name
     * @param array $spec
     */
    public function __construct($name, $spec = [])
    {
        $this->name = $name;
        parent::__construct($spec);
    }

    /**
     * @inheritDoc
     */
    public function getDecorator(): callable
    {
        return function (SchemaAwareInterface $spec) {
            $schema = new Schema($this->name);
            $schemaDecorator = parent::getDecorator();
            $schemaDecorator($schema);
            $spec->setSchema($schema);
        };
    }
}