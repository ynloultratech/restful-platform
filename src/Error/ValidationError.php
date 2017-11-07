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

namespace Ynlo\RestfulPlatformBundle\Error;

use Doctrine\Common\Util\Inflector;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Ynlo\RestfulPlatformBundle\Annotation as API;
use Ynlo\RestfulPlatformBundle\Error\ValidationError\ValidationPropertyError;
use Ynlo\RestfulPlatformBundle\Util\SerializerReader;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class ValidationError extends Error
{
    /**
     * @var string
     *
     * @Serializer\Expose()
     *
     * @API\Example("Validation failed with 1 error(s).")
     */
    protected $message;

    /**
     * @var integer
     *
     * @Serializer\Expose()
     *
     * @API\Example(Response::HTTP_UNPROCESSABLE_ENTITY)
     */
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;

    /**
     * @var ValidationPropertyError[]
     *
     * @Serializer\Expose()
     *
     * @Serializer\Type("array<Ynlo\RestfulPlatformBundle\Error\ValidationError\ValidationPropertyError>")
     */
    protected $errors = [];

    /**
     * ValidationError constructor.
     *
     * @param ConstraintViolationListInterface $violations
     */
    public function __construct(ConstraintViolationListInterface $violations)
    {
        parent::__construct(422, sprintf('Validation failed with %d error(s).', count($violations)));

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $error = new ValidationPropertyError();
            $error->setMessage($violation->getMessage());
            try {
                $error->setProperty(SerializerReader::getSerializedPropertyPath($violation->getRoot(), $violation->getPropertyPath()));
            } catch (\Exception $e) {
                $error->setProperty($violation->getPropertyPath());
            }
            $error->setInvalidValue((string) $violation->getInvalidValue());
            $error->setCode($violation->getCode());

            $this->errors[] = $error;
        }
    }
}