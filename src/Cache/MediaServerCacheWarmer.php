<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Cache;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Symfony\Component\HttpKernel\KernelEvents;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaServerMetadata;

class MediaServerCacheWarmer extends CacheWarmer implements EventSubscriberInterface
{
    /**
     * @var MediaServerMetadata
     */
    protected $metadata;


    public function __construct(MediaServerMetadata $metadata)
    {
       $this->metadata = $metadata;
    }

    /**
     * @inheritDoc
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function warmUp($cacheDir)
    {
        $this->metadata->clearCache();
    }

    /**
     * warmUp the cache on request
     * NOTE: this behavior its switched in the RestfulPlatformExtension
     */
    public function warmUpOnEveryRequest()
    {
        $this->warmUp(null);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'warmUpOnEveryRequest',
        ];
    }
}