<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Util;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Util\AnnotationReader;
use PHPUnit\Framework\TestCase;

class AnnotationReaderTest extends TestCase
{
    public function testGetAnnotationFor()
    {
        $annotation = AnnotationReader::getAnnotationFor(new \ReflectionClass(User::class), ExclusionPolicy::class);
        self::assertInstanceOf(ExclusionPolicy::class, $annotation);

        $annotation = AnnotationReader::getAnnotationFor(new \ReflectionMethod(User::class, 'getFullName'), Expose::class);
        self::assertInstanceOf(Expose::class, $annotation);

        $annotation = AnnotationReader::getAnnotationFor(new \ReflectionProperty(User::class, 'username'), Expose::class);
        self::assertInstanceOf(Expose::class, $annotation);
    }

    public function testGetAnnotationsFor()
    {
        $annotations = AnnotationReader::getAnnotationsFor(new \ReflectionClass(User::class));
        self::assertInstanceOf(ExclusionPolicy::class, $annotations[0]);

        $annotations = AnnotationReader::getAnnotationsFor(new \ReflectionMethod(User::class, 'getFullName'));
        self::assertInstanceOf(Expose::class, $annotations[0]);
        self::assertInstanceOf(VirtualProperty::class, $annotations[1]);

        $annotations = AnnotationReader::getAnnotationsFor(new \ReflectionProperty(User::class, 'username'));
        self::assertInstanceOf(Expose::class, $annotations[0]);
        self::assertInstanceOf(SerializedName::class, $annotations[1]);
    }
}
