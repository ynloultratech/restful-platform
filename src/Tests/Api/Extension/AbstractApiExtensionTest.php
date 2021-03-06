<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Api\Extension;

use Doctrine\ORM\QueryBuilder;
use Ynlo\RestfulPlatformBundle\Api\Extension\AbstractApiExtension;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\Routing\ApiRouteCollection;

class AbstractApiExtensionTest extends TestCase
{

    public function testEmptyMethods()
    {
        $api = self::createMock(RestApiInterface::class);
        $extension = new class extends AbstractApiExtension
        {

        };
        $extension->configureQuery($api, self::createMock(QueryBuilder::class));
        $extension->preUpdate($api,new \stdClass());
        $extension->postUpdate($api,new \stdClass());
        $extension->prePersist($api,new \stdClass());
        $extension->postPersist($api,new \stdClass());
        $extension->preRemove($api,new \stdClass());
        $extension->postRemove($api,new \stdClass());
        $extension->alterObject($api,new \stdClass());
        $extension->configureRoutes($api,self::createMock(ApiRouteCollection::class));

        self::assertTrue(true);
    }
}