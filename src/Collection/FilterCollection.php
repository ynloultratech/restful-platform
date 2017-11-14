<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Collection;

class FilterCollection
{
    /**
     * @var Filter[]
     */
    protected $filters = [];

    /**
     * @param                 $parameter
     * @param string|callable $field field or custom filter callback
     * @param string          $type
     * @param null            $description
     * @param null            $example
     *
     * @return $this
     */
    public function addFilter($parameter, $field = null, $type = 'string', $description = null, $example = null)
    {
        if (!$field) {
            $field = $parameter;
        }
        $this->filters[$parameter] = new Filter($parameter, $field, $type, $description, $example);

        return $this;
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}