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

namespace Ynlo\RestfulPlatformBundle\Error\ValidationError;

use JMS\Serializer\Annotation as Serializer;
use Ynlo\RestfulPlatformBundle\Annotation as API;

class ValidationPropertyError
{
    /**
     * @var string
     *
     * @API\Description("Validation message ready to display in the UI")
     * @API\Example("This value is not a valid email address.")
     *
     * @Serializer\Type("string")
     */
    protected $message = '';

    /**
     * @var string
     *
     * @API\Description("Validation error code")
     * @API\Example("EMAIL_STRICT_CHECK_FAILED_ERROR")
     *
     * @Serializer\Type("string")
     */
    protected $code = '';

    /**
     * @var string
     *
     * @API\Description("Path of the property with errors, can contains paths
     * like `user.email` or `users[0].email` for embedded resources.")
     * @API\Example("email")
     *
     * @Serializer\Type("string")
     */
    protected $property = '';

    /**
     * @var string
     *
     * @API\Description("Invalid value")
     * @API\Example("email.com")
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("invalidValue")
     */
    protected $invalidValue = '';

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return ValidationPropertyError
     */
    public function setMessage($message): ValidationPropertyError
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return ValidationPropertyError
     */
    public function setCode($code): ValidationPropertyError
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @param string $property
     *
     * @return ValidationPropertyError
     */
    public function setProperty($property): ValidationPropertyError
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvalidValue(): string
    {
        return $this->invalidValue;
    }

    /**
     * @param string $invalidValue
     *
     * @return ValidationPropertyError
     */
    public function setInvalidValue($invalidValue): ValidationPropertyError
    {
        $this->invalidValue = $invalidValue;

        return $this;
    }
}