<?php

namespace Tests\MediaServer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;
use Ynlo\RestfulPlatformBundle\MediaServer\AbstractMediaFile;
use Ynlo\RestfulPlatformBundle\MediaServer\LocalMediaStorageProvider;
use PHPUnit\Framework\TestCase;

class LocalMediaStorageProviderTest extends TestCase
{
    /**
     * @var LocalMediaStorageProvider
     */
    protected $storage;

    /**
     * @var Router|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $router;

    protected $secret = '1234567890123456789';

    protected function setUp()
    {
        $this->router = self::createMock(Router::class);
        $this->storage = new LocalMediaStorageProvider($this->router, $this->secret);
        $this->storage->setConfig(
            [
                'dir_name' => sys_get_temp_dir(),
                'base_url' => 'https://static.example.com/',
                'private' => false,
                'signature_parameter' => '_hash',
                'signature_max_age' => 3600,
            ]
        );
    }

    public function testRead()
    {
        $media = new class extends AbstractMediaFile
        {

        };
        $media->setName('test.txt');

        $content = 'Hello this is a test Message';

        $dirName = sys_get_temp_dir().'/'.$media->getUuid();
        mkdir($dirName);
        $fileName = $dirName.'/'.$media->getName();
        file_put_contents($fileName, $content);

        self::assertEquals($content, $this->storage->read($media));

        @unlink($fileName);
        @rmdir($dirName);
    }

    public function testGetDownloadUrl_PublicResource()
    {
        $media = new class extends AbstractMediaFile
        {

        };
        self::assertEquals(
            'https://static.example.com/'.$media->getUuid().'/'.$media->getName(),
            $this->storage->getDownloadUrl($media)
        );
    }

    /**
     * @group time-sensitive
     */
    public function testGetDownloadUrl_PrivateResource()
    {
        $this->storage->setConfig(
            [
                'private' => true,
                'route_name' => 'private_resource',
                'signature_parameter' => '_hash',
                'signature_max_age' => 3600,
            ]
        );
        $media = new class extends AbstractMediaFile
        {

        };
        $media->setName('logo.png');

        $route = self::createMock(Route::class);
        $route->expects(self::once())->method('getPath')->willReturn('/{uuid}/{name}');

        $collection = self::createMock(RouteCollection::class);
        $collection->expects(self::once())->method('get')->with('private_resource')->willReturn($route);

        $this->router->expects(self::once())->method('getRouteCollection')->willReturn($collection);

        $hash = $this->storage->createSignature($media);
        $this->router->expects(self::once())->method('generate')->with(
            'private_resource',
            [
                'uuid' => $media->getUuid(),
                'name' => $media->getName(),
                '_hash' => $hash,
            ],
            Router::ABSOLUTE_URL
        )->willReturn('https://static.example.com/'.$media->getUuid().'/'.$media->getName().'?_hash='.$hash);

        self::assertEquals(
            'https://static.example.com/'.$media->getUuid().'/'.$media->getName().'?_hash='.$hash,
            $this->storage->getDownloadUrl($media)
        );
    }

    public function testSave_Public()
    {
        $media = new class extends AbstractMediaFile
        {

        };
        $media->setName('logo.png');

        $content = 'Hello...';
        $this->storage->save($media, $content);

        $dirName = sys_get_temp_dir().'/'.$media->getUuid();
        $fileName = $dirName.'/'.$media->getName();

        self::assertFileIsReadable($fileName);
        self::assertFileIsWritable($fileName);
        self::assertEquals($content, file_get_contents($fileName));

        @unlink($fileName);
        @rmdir($dirName);
    }

    public function testSave_Private()
    {
        $this->storage->setConfig(
            [
                'dir_name' => sys_get_temp_dir(),
                'private' => true,
            ]
        );
        $media = new class extends AbstractMediaFile
        {

        };
        $media->setName('logo.png');

        $content = 'Hello...';
        $this->storage->save($media, $content);

        $dirName = sys_get_temp_dir().'/'.$media->getUuid();
        $fileName = $dirName.'/'.$media->getName();

        self::assertFileIsReadable($fileName);
        self::assertFileIsWritable($fileName);
        self::assertEquals($content, file_get_contents($fileName));
        self::assertNotNull($media->getStorageMetaValue('salt'));

        @unlink($fileName);
        @rmdir($dirName);
    }

    public function testRemove()
    {
        $media = new class extends AbstractMediaFile
        {

        };
        $media->setName('test.txt');

        $dirName = sys_get_temp_dir().'/'.$media->getUuid();
        mkdir($dirName);
        $fileName = $dirName.'/'.$media->getName();
        file_put_contents($fileName, '');

        self::assertFileExists($fileName);

        $this->storage->remove($media);

        self::assertFileNotExists($fileName);

        @rmdir($dirName);
    }

    /**
     * @group time-sensitive
     */
    public function testCreateSignature()
    {
        $media = new class extends AbstractMediaFile
        {

        };
        $media->setName('logo.png');

        $salt = '1234567890';
        $media->setStorageMetaValue('salt', $salt);
        $signature = $this->storage->createSignature($media);

        self::assertNotNull($signature);

        $iv = substr(preg_replace('/[^\w]/', '', $this->secret), 0, 16);
        $signatureData = unserialize(openssl_decrypt($signature, 'aes-128-cbc', $salt, 0, $iv), []);

        $expectedSignatureData = [
            'uuid' => $media->getUuid(),
            'name' => $media->getName(),
            'expire' => \DateTime::createFromFormat('U', time())->modify("+3600Seconds")->format('c'),
        ];

        self::assertEquals($expectedSignatureData, $signatureData);
    }

    public function testIsValidSignature()
    {
        $media = new class extends AbstractMediaFile
        {

        };
        $media->setName('logo.png');

        //test valid signature
        $signature = $this->storage->createSignature($media);
        self::assertTrue($this->storage->isValidSignature($media, $signature));

        //test invalid signature
        self::assertFalse($this->storage->isValidSignature($media, '0999919292'));

        //test signature from other media
        $media2 = new class extends AbstractMediaFile
        {

        };
        $signature2 = $this->storage->createSignature($media2);
        self::assertFalse($this->storage->isValidSignature($media, $signature2));
    }

    public function testIsValidSignedRequest()
    {
        $request = self::createMock(Request::class);

        $media = new class extends AbstractMediaFile
        {

        };
        $media->setName('logo.png');

        //test valid signature
        $signature = $this->storage->createSignature($media);

        $request->expects(self::once())->method('get')->with('_hash')->willReturn($signature);

        self::assertTrue($this->storage->isValidSignedRequest($media, $request));
        self::assertFalse($this->storage->isValidSignedRequest($media, Request::createFromGlobals()));
    }
}
