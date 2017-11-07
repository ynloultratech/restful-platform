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

namespace Ynlo\RestfulPlatformBundle\MediaServer;

use Symfony\Component\HttpFoundation\File\File;

interface MediaFileInterface
{
    const STATUS_NEW = 'NEW';
    const STATUS_IN_USE = 'IN_USE';

    /**
     * Unique identifier for resource
     *
     * @return mixed
     */
    public function getId();

    /**
     * Global identifier, used for share in urls etc.
     *
     * @return string
     */
    public function getUuid();

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid);

    /**
     * Name of the file
     *
     * @return string
     */
    public function getName();

    /**
     * Name of the file
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Name of the resource
     *
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     */
    public function setLabel($label);

    /**
     * MimeType of the resource
     *
     * @return string
     */
    public function getContentType();

    /**
     * MimeType of the resource
     *
     * @param string $contentType
     */
    public function setContentType($contentType);

    /**
     * Size of the resource in bytes
     *
     * @return string
     */
    public function getSize();

    /**
     * Size of the resource in bytes
     *
     * @param integer $size
     */
    public function setSize($size);

    /**
     * Url to access to the resource
     *
     * @return string
     */
    public function getDownloadUrl();

    /**
     * Url to access to the resource
     *
     * @param string $url
     */
    public function setDownloadUrl($url);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $updated
     */
    public function setCreatedAt(\DateTime $updated);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updated
     */
    public function setUpdatedAt(\DateTime $updated);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     */
    public function setStatus(string $status);


    /**
     * @return boolean
     */
    public function isNew();

    /**
     * @return boolean
     */
    public function isInUse();

    public function used();

    /**
     * @return string
     */
    public function getStorage();

    /**
     * @param string $storage
     */
    public function setStorage($storage);

    /**
     * @param array $meta
     *
     * @return mixed
     */
    public function setStorageMeta(array $meta);

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function setStorageMetaValue($key, $value);

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getStorageMetaValue($key, $default = null);

    /**
     * @return array
     */
    public function getStorageMeta();
}