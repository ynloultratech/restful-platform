<?php

namespace Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Ynlo\RestfulPlatformBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    protected $default = [

    ];

    public function testDocumentation()
    {
        $processor = new Processor();
        $configuration = new Configuration(true);
        $input = array_merge(
            $this->default,
            [
                'documentation' => [
                    'info' => [
                        'title' => 'API Title',
                        'description' => 'API Description',
                        'version' => 'v1.0',
                    ],
                    'tags' => [
                        'User',
                        'Group',
                    ],
                ],
            ]
        );
        $config = $processor->processConfiguration($configuration, [$input]);
        self::assertEquals('API Title', $config['documentation']['info']['title']);
        self::assertEquals('API Description', $config['documentation']['info']['description']);
        self::assertEquals('v1.0', $config['documentation']['info']['version']);
        self::assertEquals(['User', 'Group'], $config['documentation']['tags']);
    }

    public function testMediaServer_Disabled()
    {
        $processor = new Processor();
        $configuration = new Configuration(true);
        $input = array_merge($this->default, []);
        $config = $processor->processConfiguration($configuration, [$input]);
        self::assertFalse(isset($config['media_server']));
    }

    public function testMediaServer_EnabledWithoutClass()
    {
        $processor = new Processor();
        $configuration = new Configuration(true);
        $input = array_merge(
            $this->default,
            [
                'media_server' => [],
            ]
        );
        self::expectExceptionMessage('The child node "class" at path "restful_platform.media_server" must be configured.');
        $config = $processor->processConfiguration($configuration, [$input]);
        self::assertFalse(isset($config['media_server']));
    }

    public function testMediaServer_EnabledWithoutDefaultStorage()
    {
        $processor = new Processor();
        $configuration = new Configuration(true);
        $input = array_merge(
            $this->default,
            [
                'media_server' => [
                    'class' => \stdClass::class,
                ],
            ]
        );
        self::expectExceptionMessage('The child node "default_storage" at path "restful_platform.media_server" must be configured.');
        $config = $processor->processConfiguration($configuration, [$input]);
        self::assertFalse(isset($config['media_server']));
    }

    public function testMediaServer_EnabledWithoutAnyStorageConfigured()
    {
        $processor = new Processor();
        $configuration = new Configuration(true);
        $input = array_merge(
            $this->default,
            [
                'media_server' => [
                    'class' => \stdClass::class,
                    'default_storage' => 'public',
                ],
            ]
        );
        self::expectExceptionMessage('The child node "storage" at path "restful_platform.media_server" must be configured.');
        $config = $processor->processConfiguration($configuration, [$input]);
        self::assertFalse(isset($config['media_server']));
    }

    public function testMediaServer_Basics()
    {
        $processor = new Processor();
        $configuration = new Configuration(true);
        $input = array_merge(
            $this->default,
            [
                'media_server' => [
                    'class' => \stdClass::class,
                    'default_storage' => 'public',
                    'storage' => [
                        'public' => [],
                    ],
                ],
            ]
        );
        $config = $processor->processConfiguration($configuration, [$input]);
        self::assertEquals('/assets', $config['media_server']['path']);
        self::assertEquals([], $config['media_server']['storage']['public']);
        self::assertEquals(\stdClass::class, $config['media_server']['class']);
        self::assertEquals('public', $config['media_server']['default_storage']);
    }
}
