<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Root;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Path;
use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecContainer;

class PathSpec extends SpecContainer
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @param string $path
     * @param array  $specs
     */
    public function __construct($path, array $specs)
    {
        $this->path = $path;
        parent::__construct($specs);
    }

    /**
     * {@inheritdoc}
     */
    public function getDecorator(): callable
    {
        return function (SwaggerObject $spec) {
            $path = $spec->getPath($this->path);

            if (!$path){
                $path = new Path($this->path);
                $spec->getPaths()->set($this->path, $path);
            }

            $paramSpecs = parent::getDecorator();
            $paramSpecs($path);
        };
    }
}