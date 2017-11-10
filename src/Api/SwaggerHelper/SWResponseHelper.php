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

namespace Ynlo\RestfulPlatformBundle\Api\SwaggerHelper;

use Symfony\Component\HttpFoundation\Response;
use Ynlo\RestfulPlatformBundle\Error\ValidationError;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation\ResponseSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWOperation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWResponse;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWSchema;

class SWResponseHelper
{
    /**
     * @param array   $specs
     * @param integer $code
     *
     * @return ResponseSpec
     */
    public static function success(array $specs = [], $code = Response::HTTP_OK)
    {
        $specs = array_merge(
            [
                SWResponse::description('Successful Operation'),
            ],
            $specs
        );

        return SWOperation::response($code, $specs);
    }

    /**
     * @param array $specs
     *
     * @return ResponseSpec
     */
    public static function notFound($specs = [])
    {
        $specs = array_merge(
            [
                SWResponse::description('Not Found'),
            ],
            $specs
        );

        return SWOperation::response(Response::HTTP_NOT_FOUND, $specs);
    }

    /**
     * @param string $label
     * @param string $resourceClass
     * @param array  $exampleLinks
     * @param array  $serializerGroups
     *
     * @return ResponseSpec
     */
    public static function paginatedCollection($resourceClass, $label = null, $exampleLinks = [], $serializerGroups = [])
    {
        return SWResponseHelper::success(
            [
                SWResponse::schema(
                    sprintf('%s List', $label ?: 'Resource'),
                    [
                        SWSchema::groups($serializerGroups),
                        SWSchema::property(
                            '_links',
                            'object',
                            null,
                            [
                                SWSchema::additionalProperties('string'),
                                SWSchema::example($exampleLinks ?: ['href' => 'url']),
                            ]
                        ),
                        SWSchema::property(
                            'page',
                            'integer',
                            null,
                            [
                                SWSchema::description('Current page'),
                                SWSchema::example('1'),
                            ]
                        ),
                        SWSchema::property(
                            'limit',
                            'integer',
                            null,
                            [
                                SWSchema::description('Amount of records per page'),
                                SWSchema::example('30'),
                            ]
                        ),
                        SWSchema::property(
                            'pages',
                            'integer',
                            null,
                            [
                                SWSchema::description('Total number of pages'),
                                SWSchema::example('3'),
                            ]
                        ),
                        SWSchema::property(
                            'total',
                            'integer',
                            null,
                            [
                                SWSchema::description('Total number of records'),
                                SWSchema::example('25'),
                            ]
                        ),
                        SWSchema::property(
                            'items',
                            'array',
                            null,
                            [
                                SWSchema::items(SWSchema::model($resourceClass, $serializerGroups)),
                            ]
                        ),
                    ]
                ),
            ]
        );
    }

    /**
     * @param array $specs
     *
     * @return ResponseSpec
     */
    public static function validationError($specs = [])
    {
        $specs = array_merge(
            [
                SWResponse::description('Validation Error'),
                SWResponse::model(ValidationError::class),
            ],
            $specs
        );


        return SWOperation::response(Response::HTTP_UNPROCESSABLE_ENTITY, $specs);
    }
}