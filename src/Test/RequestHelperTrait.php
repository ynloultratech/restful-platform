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

use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Client getClient()
 */
trait RequestHelperTrait
{
    /**
     * @param string $path
     * @param array  $parameters
     */
    protected static function sendGET($path, array $parameters = [])
    {
        self::getClient()->request(Request::METHOD_GET, $path, $parameters);
    }

    /**
     * @param string       $path
     * @param string|array $content
     */
    protected static function sendPOST($path, $content)
    {
        self::getClient()->request(Request::METHOD_POST, $path, [], [], [], $content);
    }

    /**
     * @param string       $path
     * @param string|array $content
     */
    protected static function sendPUT($path, $content)
    {
        self::getClient()->request(Request::METHOD_PUT, $path, [], [], [], $content);
    }

    /**
     * @param string $path
     */
    protected static function sendDELETE($path)
    {
        self::getClient()->request(Request::METHOD_DELETE, $path);
    }
}