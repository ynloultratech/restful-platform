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

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation;

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecContainer;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter;

class ParameterSpec extends SpecContainer
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Parameter constructor.
     *
     * @param string $name
     * @param array  $specs
     */
    public function __construct($name, array $specs)
    {
        $this->name = $name;
        parent::__construct($specs);
    }

    /**
     * {@inheritdoc}
     */
    public function getDecorator(): callable
    {
        return function (Operation $operation) {
            $name = $this->name;
            $parameter = new Parameter($name);

            $paramSpecs = parent::getDecorator();
            $paramSpecs($parameter);

            $operation->getParameters()->set($name, $parameter);
        };
    }
}