<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Util;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;

class AnnotationReader
{
    static protected $reader;

    /**
     * @param \ReflectionProperty|\ReflectionMethod|\ReflectionClass $reflection
     * @param string                                                 $annotationClass
     *
     * @return null|object
     */
    static public function getAnnotationFor($reflection, $annotationClass)
    {
        if ($reflection instanceof \ReflectionClass) {
            $annotation = self::reader()->getClassAnnotation($reflection, $annotationClass);
        } elseif ($reflection instanceof \ReflectionMethod) {
            $annotation = self::reader()->getMethodAnnotation($reflection, $annotationClass);
        } else {
            $annotation = self::reader()->getPropertyAnnotation($reflection, $annotationClass);
        }

        return $annotation;
    }

    /**
     * @param \ReflectionProperty|\ReflectionMethod|\ReflectionClass $reflection
     *
     * @return null|object
     */
    static public function getAnnotationsFor($reflection)
    {
        if ($reflection instanceof \ReflectionClass) {
            $annotation = self::reader()->getClassAnnotations($reflection);
        } elseif ($reflection instanceof \ReflectionMethod) {
            $annotation = self::reader()->getMethodAnnotations($reflection);
        } else {
            $annotation = self::reader()->getPropertyAnnotations($reflection);
        }

        return $annotation;
    }

    /**
     *
     */
    static public function reader()
    {
        if (!self::$reader) {
            self::$reader = new DoctrineAnnotationReader();
        }

        return self::$reader;
    }
}