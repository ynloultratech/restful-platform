<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Api;

use Symfony\Component\HttpFoundation\Request;
use Ynlo\RestfulPlatformBundle\Controller\RootEndpointController;
use Ynlo\RestfulPlatformBundle\Routing\ApiRouteCollection;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWOperation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWSchema;

class RootEndpoint extends AbstractRestApi
{
    protected $label = 'Root';

    protected $baseRoutePattern = '/';

    protected $baseRouteName = 'restful_api';

    protected $baseControllerName = RootEndpointController::class;

    public function rootOperation()
    {
        $baseUrl = 'https://example.com/api';
        $example = [
            'User' => [
                'list' => [
                    'href' => $baseUrl.'/users{?page,limit}',
                ],
                'create' => [
                    'href' => $baseUrl.'/users',
                    'method' => 'post',
                ],
                'update' => [
                    'href' => $baseUrl.'/users/{userId}',
                    'method' => 'patch',
                ],
                'remove' => [
                    'href' => $baseUrl.'/users/{userId}',
                    'method' => 'delete',
                ],
            ],
            'Roles' => [
                'list' => [
                    'href' => $baseUrl.'/clients{?page,limit}',
                ]
            ],
        ];

        $description = <<<EOS
## Root Endpoint

Discover all categories and operations that the REST API supports. Each category may contain one or multiple operations and each operation is exposed with helpful information to make the request.

EOS;


        return [
            SWOperation::description($description),
            SWOperation::response(
                200,
                [
                    SWSchema::description('Can issue a GET request to the root endpoint to get all the endpoint categories that the REST API supports.'),
                    SWSchema::schema(
                        [
                            SWSchema::example($example),
                        ]
                    ),

                ]
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function configureRoutes(ApiRouteCollection $routes)
    {
        $routes->add(Request::METHOD_GET, 'root', '');
    }
}