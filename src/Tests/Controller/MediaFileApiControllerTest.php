<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Api\CRUDRestApi;
use Ynlo\RestfulPlatformBundle\Controller\MediaFileApiController;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Ynlo\RestfulPlatformBundle\MediaServer\AbstractMediaFile;
use Ynlo\RestfulPlatformBundle\MediaServer\LocalMediaStorageProvider;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaFileInterface;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaStorageProviderInterface;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaStorageProviderPool;

class MediaFileApiControllerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var ContainerInterface|m\MockInterface
     */
    protected $container;

    /**
     * @var MediaFileApiController
     */
    protected $controller;

    /**
     * @var CRUDRestApi|m\MockInterface
     */
    protected $api;

    protected function setUp()
    {
        $this->container = m::mock(ContainerInterface::class);
        $this->api = m::mock(CRUDRestApi::class);

        $this->controller = new MediaFileApiController();
        $this->controller->setContainer($this->container);
        $this->controller->setApi($this->api);
    }

    public function testCreateAction_WithNotValidResource()
    {
        $request = m::mock(Request::class);

        $request->shouldReceive('getContent')->withArgs([true])->andReturn(null);
        $request->shouldReceive('get')->withArgs(['name', null])->andReturn('some_name');
        $request->shouldReceive('get')->withArgs(['label', null])->andReturn('some_label');

        $headers = m::mock(HeaderBag::class);
        $headers->shouldReceive('get')->withArgs(['content-type'])->andReturn('someType');
        $headers->shouldReceive('get')->withArgs(['content-length'])->andReturn('someLength');
        $request->headers = $headers;

        $this->api->shouldReceive('getRequest')->andReturn($request);

        $media = new class extends AbstractMediaFile
        {

        };
        $this->api->shouldReceive('getSubject')->andReturn($media);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->controller->createAction());
    }

    public function testCreateAction_WithoutContentType()
    {
        $request = m::mock(Request::class);

        $request->shouldReceive('getContent')->withArgs([true])->andReturn(tmpfile());
        $request->shouldReceive('get')->withArgs(['name', null])->andReturn('some_name');
        $request->shouldReceive('get')->withArgs(['label', null])->andReturn('some_label');

        $headers = m::mock(HeaderBag::class);
        $headers->shouldReceive('get')->withArgs(['content-type'])->andReturn(null);
        $headers->shouldReceive('get')->withArgs(['content-length'])->andReturn('someLength');
        $request->headers = $headers;

        $this->api->shouldReceive('getRequest')->andReturn($request);

        $media = new class extends AbstractMediaFile
        {

        };
        $this->api->shouldReceive('getSubject')->andReturn($media);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->controller->createAction());
    }

    public function testCreateAction_WithoutContentLength()
    {
        $request = m::mock(Request::class);

        $request->shouldReceive('getContent')->withArgs([true])->andReturn(tmpfile());
        $request->shouldReceive('get')->withArgs(['name', null])->andReturn('some_name');
        $request->shouldReceive('get')->withArgs(['label', null])->andReturn('some_label');

        $headers = m::mock(HeaderBag::class);
        $headers->shouldReceive('get')->withArgs(['content-type'])->andReturn('someType');
        $headers->shouldReceive('get')->withArgs(['content-length'])->andReturn(null);
        $request->headers = $headers;

        $this->api->shouldReceive('getRequest')->andReturn($request);

        $media = new class extends AbstractMediaFile
        {

        };
        $this->api->shouldReceive('getSubject')->andReturn($media);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->controller->createAction());
    }

    public function testCreateAction_WithoutName()
    {
        $request = m::mock(Request::class);

        $request->shouldReceive('getContent')->withArgs([true])->andReturn(tmpfile());
        $request->shouldReceive('get')->withArgs(['name', null])->andReturn(null);
        $request->shouldReceive('get')->withArgs(['label', null])->andReturn('some_label');

        $headers = m::mock(HeaderBag::class);
        $headers->shouldReceive('get')->withArgs(['content-type'])->andReturn('someType');
        $headers->shouldReceive('get')->withArgs(['content-length'])->andReturn('someLength');
        $request->headers = $headers;

        $this->api->shouldReceive('getRequest')->andReturn($request);

        $media = new class extends AbstractMediaFile
        {

        };
        $this->api->shouldReceive('getSubject')->andReturn($media);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->controller->createAction());
    }

    public function testCreateAction_WithEmptyResource()
    {
        $request = m::mock(Request::class);

        $request->shouldReceive('getContent')->withArgs([true])->andReturn(tmpfile());
        $request->shouldReceive('get')->withArgs(['name', null])->andReturn('some_name');
        $request->shouldReceive('get')->withArgs(['label', null])->andReturn('some_label');

        $headers = m::mock(HeaderBag::class);
        $headers->shouldReceive('get')->withArgs(['content-type'])->andReturn('someType');
        $headers->shouldReceive('get')->withArgs(['content-length'])->andReturn('someLength');
        $request->headers = $headers;

        $this->api->shouldReceive('getRequest')->andReturn($request);

        $media = new class extends AbstractMediaFile
        {

        };
        $this->api->shouldReceive('getSubject')->andReturn($media);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->controller->createAction());
    }

    public function testCreateAction_Success()
    {
        $media = $this->commonPreUpdateAndCreate();
        list($code, $object) = $this->controller->createAction();
        self::assertEquals(Response::HTTP_CREATED, $code);
        self::assertEquals($media, $object);
    }

    public function testUpdateAction()
    {
        $media = $this->commonPreUpdateAndCreate();
        list($code, $object) = $this->controller->updateAction();
        self::assertEquals(Response::HTTP_OK, $code);
        self::assertEquals($media, $object);
    }

    public function testDownloadAction_InvalidMedia()
    {
        $media = null;
        $request = m::mock(Request::class);

        $this->commonDownload($media, $request);

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->controller->downloadAction($request)->getStatusCode());
    }

    public function testDownloadAction_InvalidMediaSignature()
    {
        $media = new class extends AbstractMediaFile
        {
            protected $storage = 'private';
        };
        $request = m::mock(Request::class);

        $this->commonDownload($media, $request);

        $storage = m::mock(LocalMediaStorageProvider::class);
        $storage->shouldReceive('isValidSignedRequest', [$media, $request])->andReturn(false);

        $storagePool = m::mock(MediaStorageProviderPool::class);
        $storagePool->shouldReceive('getByStorageId')
                    ->withArgs(['private'])->andReturn($storage);

        $this->container->shouldReceive('get')
                        ->withArgs(['restful_platform.media_storage_pool'])
                        ->andReturn($storagePool);

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->controller->downloadAction($request)->getStatusCode());
    }

    public function testDownloadAction_Success()
    {
        $fileContent = 'Foo, Bar';
        $fileName = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($fileName, $fileContent);

        $media = new class extends AbstractMediaFile
        {
            protected $storage = 'private';

            protected $contentType = 'text/plain';

            protected $size = 1024;
        };
        $request = m::mock(Request::class);

        $this->commonDownload($media, $request);

        $storage = m::mock(LocalMediaStorageProvider::class);
        $storage->shouldReceive('isValidSignedRequest')->withArgs([$media, $request])->andReturn(true);
        $storage->shouldReceive('getFileName')->andReturn($fileName);

        $storagePool = m::mock(MediaStorageProviderPool::class);
        $storagePool->shouldReceive('getByStorageId')
                    ->withArgs(['private'])->andReturn($storage);

        $this->container->shouldReceive('get')
                        ->withArgs(['restful_platform.media_storage_pool'])
                        ->andReturn($storagePool);

        /** @var BinaryFileResponse $response */
        $response = $this->controller->downloadAction($request);

        self::assertInstanceOf(BinaryFileResponse::class, $response);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertEquals('text/plain', $response->headers->get('Content-Type'));
        self::assertEquals(1024, $response->headers->get('Content-Length'));
        self::assertEquals($fileName, $response->getFile()->getPathname());

        @unlink($fileName);
    }

    protected function commonDownload($media, m\MockInterface $requestMock)
    {
        $findArgs = ['uuid' => 1234, 'name' => 'foo'];
        $requestMock->shouldReceive('get')
                    ->withArgs(['_route_params'])
                    ->andReturn($findArgs);

        $this->container->shouldReceive('getParameter')
                        ->withArgs(['restful_platform.config.media_server'])
                        ->andReturn(['class' => \stdClass::class]);

        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('findOneBy')
             ->withArgs([$findArgs])
             ->andReturn($media);

        $doctrine = m::mock(Registry::class);
        $doctrine->shouldReceive('getRepository')
                 ->withArgs([\stdClass::class])
                 ->andReturn($repo);

        $this->container->shouldReceive('get')
                        ->withArgs(['doctrine'])
                        ->andReturn($doctrine);
    }

    protected function commonPreUpdateAndCreate()
    {
        $this->container->shouldReceive('getParameter')
                        ->withArgs(['restful_platform.config.media_server'])
                        ->andReturn(['default_storage' => 'public']);

        $storage = m::mock(MediaStorageProviderInterface::class);


        $storagePool = m::mock(MediaStorageProviderPool::class);
        $storagePool->shouldReceive('getByStorageId')
                    ->withArgs(['public'])->andReturn($storage);

        $this->container->shouldReceive('get')
                        ->withArgs(['restful_platform.media_storage_pool'])
                        ->andReturn($storagePool);

        $request = m::mock(Request::class);

        $fileContent = 'foo, bar';

        $fileName = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($fileName, $fileContent);
        $file = fopen($fileName, 'r+');

        $request->shouldReceive('getContent')->withArgs([true])->andReturn($file);
        $request->shouldReceive('get')->withArgs(['name', null])->andReturn('some_name');
        $request->shouldReceive('get')->withArgs(['label', null])->andReturn('some_label');

        $headers = m::mock(HeaderBag::class);
        $headers->shouldReceive('get')->withArgs(['content-type'])->andReturn('text/plain');
        $headers->shouldReceive('get')->withArgs(['content-length'])->andReturn(1024);
        $request->headers = $headers;

        $this->api->shouldReceive('getRequest')->andReturn($request);

        $media = new class extends AbstractMediaFile
        {

        };
        $this->api->shouldReceive('getSubject')->andReturn($media);

        $manager = m::mock(EntityManager::class);
        $manager->shouldReceive('beginTransaction');
        $manager->shouldReceive('persist')->withArgs([$media]);
        $manager->shouldReceive('flush')->withArgs([$media]);
        $manager->shouldReceive('commit');
        $manager->shouldReceive('refresh');

        $this->api->shouldReceive('getManager')->andReturn($manager);

        $storage->shouldReceive('save')->withArgs([$media, $fileContent]);

        @unlink($fileName);

        return $media;
    }
}
