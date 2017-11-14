<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Path;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Path;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecContainer;

class OperationSpec extends SpecContainer
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @param string $method
     * @param array  $specs
     */
    public function __construct($method, array $specs)
    {
        $this->method = $method;
        parent::__construct($specs);
    }

    /**
     * {@inheritdoc}
     */
    public function getDecorator(): callable
    {
        return function (Path $spec) {
            $operation = new Operation();
            $paramSpecs = parent::getDecorator();
            $paramSpecs($operation);

            $spec->getOperations()->set($this->method, $operation);
        };
    }
}