<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Demo\ApiDemoBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Ynlo\RestfulPlatformBundle\Demo\ApiDemoBundle\Entity\User;

class Fixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $manager->persist($user);

        $user = new User();
        $user->setUsername('edward');
        $manager->persist($user);

        $user = new User();
        $user->setUsername('darren');
        $manager->persist($user);

        $user = new User();
        $user->setUsername('kevin');
        $manager->persist($user);

        $manager->flush();
    }
}