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

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Root;

use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecDecorator;

class InfoSpec implements SpecDecorator
{
    protected $title = '';
    protected $description = '';
    protected $version = '';

    /**
     * InfoSpec constructor.
     *
     * @param $title
     * @param $description
     * @param $version
     */
    public function __construct($title, $description = '', $version = '')
    {
        $this->title = $title;
        $this->description = $description;
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getDecorator(): callable
    {
        return function (SwaggerObject $spec) {
            $spec->getInfo()->setName($this->title);
            $spec->getInfo()->setDescription($this->description);
            $spec->getInfo()->setVersion($this->version);
        };
    }
}