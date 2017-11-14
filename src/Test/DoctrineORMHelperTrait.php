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

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * @method Client getClient()
 */
trait DoctrineORMHelperTrait
{
    public static function getDoctrine()
    {
        return self::getClient()->getKernel()->getContainer()->get('doctrine');
    }

    public static function getRepository($class): ObjectRepository
    {
        return self::getDoctrine()->getRepository($class);
    }

    public static function assertRepositoryContains($class, $criteria)
    {
        self::assertNotNull(self::getRepository($class)->findOneBy($criteria));
    }
}