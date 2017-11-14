<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Client;

class ApiTestCase extends WebTestCase
{
    use RequestHelperTrait;
    use ResponseHelperTrait;
    use JsonHelperTrait;

    private static $client;

    /**
     * @inheritdoc
     */
    protected static function createClient(array $options = [], array $server = [])
    {
        $client = parent::createClient($options, $server);
        self::$client = $client;

        return $client;
    }

    /**
     * @return Client
     */
    protected static function getClient(): Client
    {
        return self::$client ?? self::createClient();
    }
}
