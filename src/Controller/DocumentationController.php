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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DocumentationController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function docAction(Request $request): Response
    {
        $config = $this->getParameter('restful_platform.config');
        $title = @$config['documentation']['info']['title'];

        return $this->render(
            '@RestfulPlatform/doc.html.twig',
            [
                'version' => $request->get('version'),
                'title' => $title,
            ]
        );
    }

    /**
     * @return JsonResponse
     */
    public function docJsonAction(Request $request): JsonResponse
    {
        $specification = $this->get('restful_platform.api_specification')->serialize();

        //TODO: support for versioning inside the api_specification
        //FIXME: remove this statements when versioning is ready
        $specArray = json_decode($specification, true);
        $basePath = $specArray['basePath']??'';

        if (preg_match('/{version}/', $basePath)) {
            $basePath = preg_replace('/{version}/', $request->get('version'), $basePath);
            $specArray['basePath'] = $basePath;
        }

        $specification = json_encode($specArray);

        return JsonResponse::fromJsonString($specification);
    }
}