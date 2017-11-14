<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger;

use Symfony\Component\HttpFoundation\Response;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWOperation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWParameter;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWPath;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWResponse;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWSchema;
use Ynlo\RestfulPlatformBundle\Swagger\SwaggerGenerator;

class SwaggerGeneratorTest extends TestCase
{
    public function testGenerator()
    {
        $userSpec = SWSchema::schema(
            'User',
            [
                SWSchema::property(
                    'username',
                    'string',
                    null,
                    [
                        SWSchema::description('username'),
                        SWSchema::example('admin'),
                    ]
                ),
                SWSchema::property(
                    'firstName',
                    'string',
                    null,
                    [
                        SWSchema::description('First name'),
                        SWSchema::example('John'),
                    ]
                ),
                SWSchema::property(
                    'lastName',
                    'string',
                    null,
                    [
                        SWSchema::description('Last name'),
                        SWSchema::example('Smith'),
                    ]
                ),
            ]
        );

        $specs[] = SWObject::info('API Example', 'Some API Example', 'v1.0');
        $specs[] = SWObject::host('example.com');
        $specs[] = SWObject::basePath('/v1');

        $specs[] = SWObject::path(
            '/users',
            [
                SWPath::get(
                    [
                        SWOperation::tag('Users'),
                        SWOperation::parameter(
                            'q',
                            [
                                SWParameter::inQuery('string'),
                                SWParameter::description('Term to search users'),
                            ]
                        ),
                        SWOperation::description('Get list of users'),
                        SWOperation::response(
                            200,
                            [
                                SWResponse::description('List of users'),
                                SWResponse::schema(
                                    'USer',
                                    [
                                        SWSchema::type('array'),
                                        SWSchema::items($userSpec),
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $specs[] = SWObject::path(
            '/users',
            [
                SWPath::post(
                    [
                        SWOperation::tag('Users'),
                        SWOperation::description('Create user'),
                        SWOperation::body($userSpec),
                        SWOperation::response(
                            Response::HTTP_CREATED,
                            [
                                SWResponse::emptyResponse(),
                                SWResponse::description('User created successfully'),
                            ]
                        ),
                        SWOperation::response(
                            Response::HTTP_BAD_REQUEST,
                            [
                                SWResponse::emptyResponse(),
                                SWResponse::description('Error creating user'),
                                SWResponse::schema(
                                    [
                                        SWSchema::property('code', 'number', null, [SWSchema::example(200)]),
                                        SWSchema::property('error', 'string', null, [SWSchema::example('username is required')]),
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $generator = new SwaggerGenerator();
        $json = $generator->generate($specs, 'json', JSON_PRETTY_PRINT);

        $expectedJson = file_get_contents(__DIR__.'/../Fixtures/api_swagger.json');
        self::assertEquals($expectedJson, $json);
    }
}
