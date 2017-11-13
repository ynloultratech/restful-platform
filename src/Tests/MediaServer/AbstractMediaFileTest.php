<?php

namespace Ynlo\RestfulPlatformBundle\Tests\MediaServer;

use Ynlo\RestfulPlatformBundle\MediaServer\AbstractMediaFile;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaFileInterface;

class AbstractMediaFileTest extends TestCase
{
    /**
     * @var AbstractMediaFile
     */
    protected $mediaFile;

    protected function setUp()
    {
        $this->mediaFile = new class extends AbstractMediaFile
        {
        };
    }

    public function testVersion()
    {
        self::assertNull($this->mediaFile->getId());
    }

    public function testUuid()
    {
        self::assertThat(
            $this->mediaFile->getUuid(),
            self::matchesRegularExpression('/(\w){8}-(\w){4}-(\w){4}-(\w){4}-(\w){12}/')
        );

        $value = '123';
        self::assertEquals($value, $this->mediaFile->setUuid($value)->getUuid());
    }

    public function testName()
    {
        $value = 'name';
        self::assertEquals($value, $this->mediaFile->setName($value)->getName());
    }

    public function testContentType()
    {
        $value = 'jpeg';
        self::assertEquals($value, $this->mediaFile->setContentType($value)->getContentType());
    }

    public function testLabel()
    {
        $value = 'label';
        self::assertEquals($value, $this->mediaFile->setLabel($value)->getLabel());
    }

    public function testSize()
    {
        $value = 200;
        self::assertEquals($value, $this->mediaFile->setSize($value)->getSize());
    }

    public function testCreatedAt()
    {
        $value = new \DateTime();
        self::assertEquals($value, $this->mediaFile->setCreatedAt($value)->getCreatedAt());
    }

    public function testUpdatedAt()
    {
        $value = new \DateTime();
        self::assertEquals($value, $this->mediaFile->setUpdatedAt($value)->getUpdatedAt());
    }

    public function testDownloadUrl()
    {
        $value = 'https://example.com/logo.png';
        self::assertEquals($value, $this->mediaFile->setDownloadUrl($value)->getDownloadUrl());
    }

    public function testStatus()
    {
        self::assertEquals(MediaFileInterface::STATUS_NEW, $this->mediaFile->getStatus());
        self::assertTrue($this->mediaFile->isNew());

        $this->mediaFile->setStatus(MediaFileInterface::STATUS_IN_USE);
        self::assertTrue($this->mediaFile->isInUse());
    }

    public function testStorage()
    {
        $value = 'local';
        self::assertEquals($value, $this->mediaFile->setStorage($value)->getStorage());
    }

    public function testStorageMeta()
    {
        $value = ['foo' => 1, 'bar' => 2];
        self::assertEquals($value, $this->mediaFile->setStorageMeta($value)->getStorageMeta());
        self::assertEquals($value['foo'], $this->mediaFile->getStorageMetaValue('foo'));

        $this->mediaFile->setStorageMetaValue('foo', 2);
        self::assertEquals(2, $this->mediaFile->getStorageMetaValue('foo'));
    }

    public function testStringVersion()
    {
        $url = 'http:/example.com/logo.png';
        $this->mediaFile->setDownloadUrl($url);
        self::assertEquals($url, (string) $this->mediaFile);
    }
}
