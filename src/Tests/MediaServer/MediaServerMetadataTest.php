<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\MediaServer;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Annotation\AttachMediaFile;
use Ynlo\RestfulPlatformBundle\MediaServer\AbstractMediaFile;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaServerMetadata;
use Mockery as m;

class MediaServerMetadataTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $em;

    /**
     * @var MediaServerMetadata
     */
    protected $metadata;

    protected function setUp()
    {
        $this->em = self::createMock(EntityManager::class);

        $doctrine = m::mock(Registry::class);
        $doctrine->shouldReceive('getManager')->andReturn($this->em);

        $this->metadata = new MediaServerMetadata($doctrine, sys_get_temp_dir());
    }

    public function testMetadataCache()
    {
        $cacheFile = sys_get_temp_dir().'/media_server.meta';
        if (file_exists($cacheFile)) {
            @unlink($cacheFile);
        }

        $object1 = new class
        {
            /**
             * @AttachMediaFile(storage="local")
             */
            protected $logo;

            /**
             * @AttachMediaFile(storage="other")
             */
            protected $thumbnail;

            protected $attachment;
        };

        $properties = ['logo', 'thumbnail', 'attachment'];

        $metadata1 = self::createMock(ClassMetadata::class);
        $metadata1->method('getAssociationNames')->willReturn($properties);
        $metadata1->expects(self::at(1))
                  ->method(
                      'getAssociationTargetClass'
                  )->with('logo')
                  ->willReturn(AbstractMediaFile::class);

        $metadata1->expects(self::at(2))
                  ->method(
                      'getAssociationTargetClass'
                  )->with('thumbnail')
                  ->willReturn(AbstractMediaFile::class);

        $metadata1->expects(self::at(3))
                  ->method(
                      'getAssociationTargetClass'
                  )->with('attachment')
                  ->willReturn(AbstractMediaFile::class);

        $metadata1->reflClass = new \ReflectionClass($object1);
        $metadata1->name = 'Namespace\Object';

        $allMetadata = [$metadata1];

        $metadataFactory = self::createMock(ClassMetadataFactory::class);
        $metadataFactory->expects(self::once())->method('getAllMetadata')->willReturn($allMetadata);

        $this->em->expects(self::once())->method('getMetadataFactory')->willReturn($metadataFactory);
        $entities = $this->metadata->getManagedEntities();

        self::assertCount(1, $entities);
        self::assertEquals('local', $entities['Namespace\Object']['logo']->storage);
        self::assertEquals('other', $entities['Namespace\Object']['thumbnail']->storage);
        self::assertEquals('', $entities['Namespace\Object']['attachment']->storage);

        self::assertTrue($this->metadata->isMappedClass('Namespace\Object'));
        self::assertTrue($this->metadata->isMappedProperty('Namespace\Object', 'logo'));
        self::assertEquals('local', $this->metadata->getPropertyConfig('Namespace\Object', 'logo')->storage);

        self::assertFileExists($cacheFile);
        self::assertFileIsWritable($cacheFile);

        //using already loaded entities
        self::assertEquals($entities, $this->metadata->getManagedEntities());

        //force use cache
        $ref = new \ReflectionProperty(MediaServerMetadata::class, 'managedEntities');
        $ref->setAccessible(true);
        $ref->setValue($this->metadata, false);
        self::assertEquals($entities, $this->metadata->getManagedEntities());


        $this->metadata->clearCache();
        self::assertFileNotExists($cacheFile);
    }
}
