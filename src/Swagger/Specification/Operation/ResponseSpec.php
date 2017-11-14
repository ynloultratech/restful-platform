<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation;

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecContainer;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Response;

class ResponseSpec extends SpecContainer
{
    /**
     * @var string
     */
    protected $code;

    /**
     * Parameter constructor.
     *
     * @param string $code
     * @param array  $specs
     */
    public function __construct($code, array $specs)
    {
        $this->code = $code;
        parent::__construct($specs);
    }

    /**
     * {@inheritdoc}
     */
    public function getDecorator(): callable
    {
        return function (Operation $operation) {
            if ($operation->getResponses()->get($this->code)){
                $response = $operation->getResponses()->get($this->code);
            }else {
                $response = new Response($this->code);
            }

            $responseSpecs = parent::getDecorator();
            $responseSpecs($response);

            $operation->getResponses()->set($this->code, $response);
        };
    }
}