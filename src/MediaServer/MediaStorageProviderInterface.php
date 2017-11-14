<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\MediaServer;

use Symfony\Component\HttpFoundation\File\File;

interface MediaStorageProviderInterface
{

    /**
     * Set resource configuration
     *
     * @param array $config
     *
     * @return mixed
     */
    public function setConfig(array $config);

    /**
     * Read resource
     *
     * @param MediaFileInterface $media
     *
     * @return string file content as string
     *
     * @throws \Exception on fail
     */
    public function read(MediaFileInterface $media);

    /**
     * Resolve the given media and get the url
     *
     * @param MediaFileInterface $media
     */
    public function getDownloadUrl(MediaFileInterface $media);

    /**
     * @param MediaFileInterface $media
     * @param string             $content resource content as string
     *
     * @throws \Exception on fail
     */
    public function save(MediaFileInterface $media, string $content);

    /**
     * Delete the file related to the resource
     *
     * @param MediaFileInterface $media resource containing the file
     *
     * @throws \Exception on fail
     */
    public function remove(MediaFileInterface $media);
}