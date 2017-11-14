<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter;

class RootEndpointController extends RestApiController
{
    public function rootAction(Request $request)
    {
        $spec = $this->get('restful_platform.api_specification');
        $paths = $spec->getSpecification()->getPaths();

        $endPoints = [];

        $basePath = preg_replace('/\/$/', '', $request->getUri());

        foreach ($paths as $path => $pathInfo) {
            foreach ($pathInfo->getOperations() as $method => $operation) {
                if (!$operation->getTags()->isEmpty()) {
                    $tag = $operation->getTags()->first();
                    if (!isset($endPoints[$tag])) {
                        $endPoints[$tag] = [];
                    }

                    list(, $name) = explode('_', $operation->getOperationId());

                    $endPoints[$tag][$name]['href'] = $basePath.$path;
                    if ($method !== 'get') {
                        $endPoints[$tag][$name]['method'] = $method;
                    }

                    $queryParams = $operation->getParameters()->filter(
                        function (Parameter $parameter) {
                            return $parameter->getIn() === Parameter::IN_QUERY;
                        }
                    );
                    if (!$queryParams->isEmpty()) {
                        $paramNames = [];
                        /** @var Parameter $param */
                        foreach ($queryParams as $param) {
                            $paramNames[] = $param->getName();
                        }
                        $queryParamsArray = implode(',', $paramNames);
                        $endPoints[$tag][$name]['href'] .= "{?$queryParamsArray}";
                    }
                }
            }
        }

        return new JsonResponse($endPoints);
    }
}