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
    public function docJsonAction(): JsonResponse
    {
        $specification = $this->get('restful_platform.api_specification')->serialize();

        return JsonResponse::fromJsonString($specification);
    }
}