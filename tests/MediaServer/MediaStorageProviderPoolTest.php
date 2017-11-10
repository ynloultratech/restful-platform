<?php

namespace Tests\MediaServer;

use Ynlo\RestfulPlatformBundle\MediaServer\LocalMediaStorageProvider;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaStorageProviderPool;
use PHPUnit\Framework\TestCase;

class MediaStorageProviderPoolTest extends TestCase
{
    public function testStoragePool()
    {
        $config = [
            'storage' => [
                'private' => [
                    'local' => [
                        'dir_name' => sys_get_temp_dir(),
                    ],
                ],
                'other' => [
                    'cnd' => [

                    ],
                ],
            ],
        ];

        $localStorageProvider = self::createMock(LocalMediaStorageProvider::class);

        $storagePool = new MediaStorageProviderPool($config);
        $storagePool->add('local', $localStorageProvider);
        $privateStorageProvider = $storagePool->getByStorageId('private');

        self::assertInstanceOf(LocalMediaStorageProvider::class, $privateStorageProvider);

        self::assertNull($storagePool->getByStorageId('other'));
    }
}
