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

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ynlo\RestfulPlatformBundle\MediaServer\LocalMediaStorageProvider;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaFileInterface;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaStorageProviderInterface;

class MediaFileApiController extends RestApiController
{
    /**
     * @inheritdoc
     */
    public function createAction()
    {
        /** @var MediaFileInterface $media */
        $media = $this->getSubject();

        $request = $this->api->getRequest();

        $mediaResource = $request->getContent(true);
        $contentType = $request->headers->get('content-type');
        $contentLength = $request->headers->get('content-length');
        $name = $request->get('name', $media->getName());
        $label = $request->get('label', $media->getLabel());

        if (!is_resource($mediaResource)
            || !$contentType
            || !$contentLength
            || !$name
        ) {
            return Response::HTTP_BAD_REQUEST;
        }
        $content = stream_get_contents($mediaResource);
        fclose($mediaResource);
        if (!$content) {
            return Response::HTTP_BAD_REQUEST;
        }

        $media->setName($name);
        $media->setLabel($label);
        $media->setContentType($contentType);
        $media->setSize($contentLength);
        $media->setUpdatedAt(new \DateTime());

        if (!$media->getStorage()) {
            $media->setStorage($this->getDefaultStorageId());
        }

        $em = $this->api->getManager();

        $em->beginTransaction();
        $em->persist($media);
        $em->flush($media);
        try {
            $this->getStorageProvider($media->getStorage())->save($media, $content);
            $em->flush($media);
            $em->commit();
        } catch (\Exception $exception) {
            $em->rollback();
            throw $exception;
        }

        $em->refresh($media);

        return [Response::HTTP_CREATED, $media];
    }

    /**
     * @inheritdoc
     */
    public function updateAction()
    {
        $response = $this->createAction();
        if (is_array($response)) {
            list(, $media) = $response;

            return [Response::HTTP_OK, $media];
        }

        return $response;
    }

    /**
     * Download action for private resources
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function downloadAction(Request $request)
    {
        //api context is not possible here because its a action out of api
        $class = $this->container->getParameter('restful_platform.config.media_server')['class'];
        $media = $this->container->get('doctrine')->getRepository($class)->findOneBy($request->get('_route_params'));
        if ($media instanceof MediaFileInterface) {
            $provider = $this->getStorageProvider($media->getStorage());

            if (($provider instanceof LocalMediaStorageProvider) && $provider->isValidSignedRequest($media, $request)) {
                return new BinaryFileResponse(
                    new File($provider->getFileName($media)), 200, [
                        'Content-Type' => $media->getContentType(),
                        'Content-Length' => $media->getSize(),
                    ]
                );
            }
        }

        return new Response(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * @return mixed
     */
    protected function getDefaultStorageId()
    {
        return $this->getParameter('restful_platform.config.media_server')['default_storage'];
    }

    /**
     * @param $storageId
     *
     * @return MediaStorageProviderInterface
     */
    protected function getStorageProvider($storageId)
    {
        return $this->get('restful_platform.media_storage_pool')->getByStorageId($storageId);
    }
}