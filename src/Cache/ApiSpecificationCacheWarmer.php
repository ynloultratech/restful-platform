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

namespace Ynlo\RestfulPlatformBundle\Cache;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Symfony\Component\HttpKernel\KernelEvents;
use Ynlo\RestfulPlatformBundle\Api\RestApiSpecification;

class ApiSpecificationCacheWarmer extends CacheWarmer implements EventSubscriberInterface
{
    /**
     * @var RestApiSpecification
     */
    protected $restApiSpecification;

    /**
     * @param RestApiSpecification $restApiSpecification
     */
    public function __construct(RestApiSpecification $restApiSpecification)
    {
        $this->restApiSpecification = $restApiSpecification;
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
        $this->restApiSpecification->clearCache();
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