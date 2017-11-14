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
