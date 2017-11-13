<?php

namespace Ynlo\RestfulPlatformBundle\Tests\EventListener\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ynlo\RestfulPlatformBundle\Annotation\AttachMediaFile;
use Ynlo\RestfulPlatformBundle\EventListener\Doctrine\MediaServerListener;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Ynlo\RestfulPlatformBundle\MediaServer\AbstractMediaFile;
use Ynlo\RestfulPlatformBundle\MediaServer\LocalMediaStorageProvider;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaServerMetadata;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaStorageProviderInterface;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaStorageProviderPool;

class MediaServerListenerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var ContainerInterface|m\MockInterface
     */
    protected $container;

    /**
     * @var MediaStorageProviderPool|m\MockInterface
     */
    protected $pool;

    /**
     * @var MediaServerListener
     */
    protected $listener;

    protected function setUp()
    {
        $this->container = m::mock(ContainerInterface::class);

        $this->pool = m::mock(MediaStorageProviderPool::class);
        $this->container->shouldReceive('get')
                        ->withArgs(['restful_platform.media_storage_pool'])
                        ->andReturn($this->pool);

        $this->listener = new MediaServerListener();
        $this->listener->setContainer($this->container);
    }

    public function testSubscribedEvents()
    {
        self::assertEquals(
            [
                Events::postLoad => 'postLoad',
                Events::preUpdate => 'preUpdate',
                Events::preRemove => 'preRemove',
            ],
            $this->listener->getSubscribedEvents()
        );
    }

    public function testPostLoad()
    {
        $event = m::mock(LifecycleEventArgs::class);

        $media = new class extends AbstractMediaFile
        {
            protected $storage = 'public';
        };
        $event->shouldReceive('getObject')->andReturns($media);

        $downloadUrl = 'https://example.com/foo/bar.png';
        $storage = m::mock(MediaStorageProviderInterface::class);
        $storage->shouldReceive('getDownloadUrl')->withArgs([$media])->andReturn($downloadUrl);

        $this->pool->shouldReceive('getByStorageId')->withArgs(['public'])->andReturn($storage);

        $this->listener->postLoad($event);

        self::assertEquals($downloadUrl, $media->getDownloadUrl());
    }

    public function testPreUpdate()
    {
        $event = m::mock(PreUpdateEventArgs::class);

        $mediaContainer = new \stdClass();
        $media = new class extends AbstractMediaFile
        {
            protected $storage = 'public';
        };
        $class = get_class($mediaContainer);

        $event->shouldReceive('getObject')->andReturns($mediaContainer);
        $event->shouldReceive('getEntityChangeSet')->andReturns(['logo' => [null, $media]]);
        $meta = m::mock(MediaServerMetadata::class);

        $this->container->shouldReceive('get')
                        ->withArgs(['restful_platform.media_server.metadata'])
                        ->andReturn($meta);

        $meta->shouldReceive('isMappedClass')->withArgs([$class])->andReturnTrue();
        $meta->shouldReceive('isMappedProperty')->withArgs([$class, 'logo'])->andReturnTrue();

        $config = new AttachMediaFile();
        $config->storage = 'private';

        $meta->shouldReceive('getPropertyConfig')->withArgs([$class, 'logo'])->andReturn($config);

        $content = 'Foo, Bar';
        $downloadUrl = 'https://example.com/foo/bar.png';

        $publicStorage = m::mock(LocalMediaStorageProvider::class);
        $publicStorage->shouldReceive('read')->withArgs([$media])->andReturn($content);
        $publicStorage->shouldReceive('remove')->withArgs([$media]);
        $this->pool->shouldReceive('getByStorageId')->withArgs(['public'])->andReturn($publicStorage);

        $privateStorage = m::mock(LocalMediaStorageProvider::class);
        $privateStorage->shouldReceive('save')->withArgs([$media, $content]);
        $privateStorage->shouldReceive('getDownloadUrl')->withArgs([$media])->andReturn($downloadUrl);
        $this->pool->shouldReceive('getByStorageId')->withArgs(['private'])->andReturn($privateStorage);

        self::assertEquals('public', $media->getStorage());
        self::assertFalse($media->isInUse());

        $this->listener->preUpdate($event);

        self::assertEquals('private', $media->getStorage());
        self::assertTrue($media->isInUse());
        self::assertEquals($downloadUrl, $media->getDownloadUrl());
    }

    public function testPreRemove()
    {
        $event = m::mock(LifecycleEventArgs::class);

        $media = new class extends AbstractMediaFile
        {
            protected $storage = 'public';
        };
        $event->shouldReceive('getObject')->andReturns($media);

        $storage = m::mock(MediaStorageProviderInterface::class);
        $storage->shouldReceive('remove')->withArgs([$media]);

        $this->pool->shouldReceive('getByStorageId')->withArgs(['public'])->andReturn($storage);

        $this->listener->preRemove($event);
    }
}
