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

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Ynlo\RestfulPlatformBundle\Annotation\Example;

/**
 * @Serializer\ExclusionPolicy("all")
 */
abstract class AbstractMediaFile implements MediaFileInterface
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose()
     * @Serializer\ReadOnly()
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="guid", unique=true)
     */
    protected $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     * @Serializer\ReadOnly()
     * @Serializer\Type("string")
     *
     * @Example("attachment.zip")
     */
    protected $name;

    /**
     * Current status of the file
     *
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    protected $status = self::STATUS_NEW;

    /**
     * @var string
     *
     * @ORM\Column(name="content_type", type="string", nullable=false)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\SerializedName("contentType")
     * @Serializer\Expose()
     * @Serializer\ReadOnly()
     * @Serializer\Type("string")
     *
     * @Example("application/zip")
     */
    protected $contentType;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\ReadOnly()
     * @Serializer\Type("string")
     *
     * @Example("Short description")
     */
    protected $label;

    /**
     * Size in bytes
     *
     * @var integer
     *
     * @ORM\Column(name="size", type="string", nullable=false)
     *
     * @Serializer\Expose()
     * @Serializer\ReadOnly()
     * @Serializer\Type("integer")
     *
     * @Example(1024)
     */
    protected $size = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     *
     * @Serializer\SerializedName("createdAt")
     * @Serializer\Expose()
     * @Serializer\ReadOnly()
     * @Serializer\Type("DateTime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     *
     * @Serializer\SerializedName("updatedAt")
     * @Serializer\Expose()
     * @Serializer\ReadOnly()
     * @Serializer\Type("DateTime")
     */
    protected $updatedAt;

    /**
     * Url to get the file
     *
     * @var string
     *
     * @Example("https://api.woorefill.com/media/837712-392391230023023/attachment.zip")
     */
    protected $downloadUrl;

    /**
     * Storage name used to save the file
     *
     * @var string
     *
     * @ORM\Column(name="storage", type="string", nullable=false)
     */
    protected $storage;

    /**
     * Each storage can save some meta required to recover the file
     *
     * @var array
     *
     * @ORM\Column(name="storage_meta", type="json_array", nullable=true)
     */
    protected $storageMeta = [];

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();

        $random = random_bytes(32);
        $random[6] = chr(ord($random[6]) & 0x0f | 0x40);    // set version to 0100
        $random[8] = chr(ord($random[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        $this->uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($random), 4));
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     *
     * @return AbstractMediaFile
     */
    public function setUuid(string $uuid): AbstractMediaFile
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return AbstractMediaFile
     */
    public function setName($name): AbstractMediaFile
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $mimeType
     *
     * @return AbstractMediaFile
     */
    public function setContentType($mimeType): AbstractMediaFile
    {
        $this->contentType = $mimeType;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return AbstractMediaFile
     */
    public function setLabel($label): AbstractMediaFile
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param integer $size
     */
    public function setSize($size): AbstractMediaFile
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return AbstractMediaFile
     */
    public function setCreatedAt(\DateTime $createdAt): AbstractMediaFile
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return AbstractMediaFile
     */
    public function setUpdatedAt(\DateTime $updatedAt): AbstractMediaFile
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->downloadUrl;
    }

    /**
     * @param string $downloadUrl
     *
     * @return AbstractMediaFile
     */
    public function setDownloadUrl($downloadUrl): AbstractMediaFile
    {
        $this->downloadUrl = $downloadUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return AbstractMediaFile
     */
    public function setStatus(string $status): AbstractMediaFile
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isNew()
    {
        return $this->getStatus() === self::STATUS_NEW;
    }

    /**
     * @inheritDoc
     */
    public function isInUse()
    {
        return $this->getStatus() === self::STATUS_IN_USE;
    }

    public function used()
    {
        $this->setStatus(self::STATUS_IN_USE);
    }

    /**
     * @return string
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param string $storage
     *
     * @return AbstractMediaFile
     */
    public function setStorage($storage): AbstractMediaFile
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @return array
     */
    public function getStorageMeta()
    {
        return $this->storageMeta;
    }

    /**
     * @param array $storageMeta
     *
     * @return AbstractMediaFile
     */
    public function setStorageMeta(array $storageMeta): AbstractMediaFile
    {
        $this->storageMeta = $storageMeta;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setStorageMetaValue($key, $value)
    {
        $this->storageMeta[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function getStorageMetaValue($key, $default = null)
    {
        if (isset($this->storageMeta[$key])) {
            return $this->storageMeta[$key];
        }

        return $default;
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getDownloadUrl() ?: '';
    }
}
