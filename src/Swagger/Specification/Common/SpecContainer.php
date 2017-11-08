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

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Common;

use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerSpecModel;

/**
 * Can contains multiple decorators inside a array
 */
class SpecContainer implements SpecDecorator
{
    /**
     * @var SpecDecorator[]
     */
    protected $specs;

    /**
     * @param $specs
     */
    public function __construct($specs)
    {
        $this->specs = $specs;
    }

    /**
     * {@inheritdoc}
     */
    public function getDecorator(): callable
    {
        return function (SwaggerSpecModel $specification) {
            foreach ($this->specs as $spec) {
                if (is_array($spec)) {
                    $spec = new SpecContainer($spec);
                }

                if ($spec instanceof SpecDecorator) {
                    $decorator = $spec->getDecorator();
                    $decorator($specification);
                }
            }
        };
    }
}