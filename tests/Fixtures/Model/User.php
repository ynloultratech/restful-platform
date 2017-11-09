<?php

namespace Tests\Fixtures\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class User
{
    /**
     * @var string
     *
     * @Serializer\Expose()
     * @Serializer\SerializedName("username")
     * @Serializer\Type("string")
     */
    protected $username;

    /**
     * @var string
     *
     * @Serializer\Expose()
     * @Serializer\SerializedName("first_name")
     * @Serializer\Type("string")
     *
     * @Serializer\Groups({"public"})
     */
    protected $firstName;

    /**
     * @var string
     *
     * @Serializer\Expose()
     * @Serializer\SerializedName("last_name")
     * @Serializer\Type("string")
     *
     * @Serializer\Groups({"public"})
     */
    protected $lastName;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @Serializer\Expose()
     * @Serializer\VirtualProperty("fullName")
     * @Serializer\SerializedName("fullName")
     * @Serializer\Type("string")
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }
}