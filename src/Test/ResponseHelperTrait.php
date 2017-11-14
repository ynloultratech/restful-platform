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

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method Client getClient()
 */
trait ResponseHelperTrait
{
    protected static function assertResponseEmptyContent()
    {
        self::assertEmpty(self::getClient()->getResponse()->getContent());
    }

    protected static function assertResponseCodeIs($code)
    {
        self::assertEquals($code, self::getClient()->getResponse()->getStatusCode());
    }

    protected static function assertResponseCodeIsOK()
    {
        self::assertEquals(Response::HTTP_OK, self::getClient()->getResponse()->getStatusCode());
    }

    protected static function assertResponseCodeIsNotFound()
    {
        self::assertEquals(Response::HTTP_NOT_FOUND, self::getClient()->getResponse()->getStatusCode());
    }

    protected static function assertResponseCodeIsNoContent()
    {
        self::assertEquals(Response::HTTP_NO_CONTENT, self::getClient()->getResponse()->getStatusCode());
    }

    protected static function assertResponseCodeIsCreated()
    {
        self::assertEquals(Response::HTTP_CREATED, self::getClient()->getResponse()->getStatusCode());
    }

    protected static function assertResponseCodeIsForbidden()
    {
        self::assertEquals(Response::HTTP_FORBIDDEN, self::getClient()->getResponse()->getStatusCode());
    }

    protected static function assertResponseCodeIsUnprocessableEntity()
    {
        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, self::getClient()->getResponse()->getStatusCode());
    }

    /**
     * @return Response
     */
    protected static function getResponse(): Response
    {
        return self::getClient()->getResponse();
    }
}