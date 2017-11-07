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

namespace Ynlo\RestfulPlatformBundle\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ynlo\RestfulPlatformBundle\Api\SwaggerHelper\SWResponseHelper;
use Ynlo\RestfulPlatformBundle\Controller\MediaFileApiController;
use Ynlo\RestfulPlatformBundle\Routing\ApiRouteCollection;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWOperation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWParameter;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWResponse;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWSchema;

class MediaFileApi extends CRUDRestApi
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $baseControllerName = MediaFileApiController::class;

    /**
     * @param array $config Media server configuration
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->resourceClass = $config['class'];
        $this->baseRoutePattern = $config['path'];

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function createOperation()
    {
        $specs = $this->commonAssetSpecs();
        $specs[] = SWResponseHelper::success([SWResponse::model($this->getResourceClass())], Response::HTTP_CREATED);

        $specs[] = SWOperation::parameter(
            'name',
            [
                SWParameter::inPath('string'),
            ]
        );

        return $specs;
    }

    /**
     * @inheritDoc
     */
    public function updateOperation()
    {
        $specs = $this->commonAssetSpecs();
        $specs[] = SWResponseHelper::success([SWResponse::model($this->getResourceClass())]);

        return $specs;
    }

    protected function commonAssetSpecs()
    {
        $path = $this->getBaseRoutePattern();
        $doc = <<<EOS
        
The asset data is expected in its raw binary form, rather than JSON. 
Everything else about the endpoint is the same as the rest of the API. 
For example, you'll still need to pass your authentication to be able to upload an asset.

### Example:

    POST https://...$path?name=foo.zip
<br>
The raw file is uploaded to the server. 
Set the content type and length appropriately in the header, and the asset's name and label in URI query parameters.
EOS;

        return [
            SWOperation::tag($this->getLabel()),
            SWOperation::description($doc),
            SWOperation::body([SWSchema::type('binary')]),
            SWOperation::parameter(
                'Content-Type',
                [
                    SWParameter::inHeader('string'),
                    SWParameter::required(true),
                    SWParameter::description('The content type of the asset. This should be set in the Header. Example: "application/zip"'),
                ]
            ),
            SWOperation::parameter(
                'Content-Length',
                [
                    SWParameter::inHeader('integer'),
                    SWParameter::required(true),
                    SWParameter::description('Size in bytes of the content'),
                ]
            ),
            SWOperation::parameter(
                'name',
                [
                    SWParameter::inQuery('string'),
                    SWParameter::description('The file name of the asset. This should be set in a URI query parameter.'),
                ]
            ),
            SWOperation::parameter(
                'label',
                [
                    SWParameter::inQuery('string'),
                    SWParameter::description('An alternate short description of the asset. Used in place of the filename. This should be set in a URI query parameter.'),
                ]
            ),
            SWOperation::response(Response::HTTP_BAD_REQUEST, [SWResponse::description('Bad Request: Some required parameter is missing, like name or Content-Type')]),
            SWOperation::response(Response::HTTP_BAD_GATEWAY, [SWResponse::description('Upstream failure')]),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function loadSubjectFromRequest(Request $request)
    {
        parent::loadSubjectFromRequest($request);

        if ($request->getMethod() === Request::METHOD_POST) {
            $this->subject = new $this->resourceClass;
        }
    }

    protected function configureRoutes(ApiRouteCollection $routes)
    {
        parent::configureRoutes($routes);
        $routes->get('create')->setPath($this->getBaseRoutePattern().'/{name}');
        $routes->get('update')->setMethods([Request::METHOD_PUT]);
        $routes->clearExcept($this->config['actions']);
    }
}