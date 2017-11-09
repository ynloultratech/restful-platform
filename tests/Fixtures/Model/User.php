<?php

namespace Tests\Fixtures\Model;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Ynlo\RestfulPlatformBundle\Annotation\Example;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @Serializer\AccessorOrder("custom", custom={
 *              "firstName",
 *              "lastName",
 *              "username",
 *              "getFullName",
 *              "admin",
 *              "groups",
 *     })
 *
 * @Serializer\VirtualProperty(
 *     "admin",
 *     exp="object.getName() == 'admin'",
 *     options={
 *       @Serializer\SerializedName("admin"),
 *       @Serializer\Type("boolean"),
 *       @Serializer\Groups("private"),
 *     }
 *  )
 */
class User
{
    /**
     * @var string
     *
     * @Serializer\Expose()
     * @Serializer\SerializedName("username")
     * @Serializer\Type("string")
     * @Serializer\ReadOnly()
     * @Serializer\Groups({"private"})
     *
     * @Example("admin")
     */
    protected $username;

    /**
     * @var string
     *
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\Groups({"public"})
     *
     * @Assert\NotBlank()
     *
     * @Example("John")
     */
    protected $firstName;

    /**
     * @var string
     *
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\Groups({"public"})
     *
     * @Assert\NotBlank()
     *
     * @Example("Smith")
     */
    protected $lastName;

    /**
     * @var User
     *
     * @Serializer\Expose()
     * @Serializer\Type("Tests\Fixtures\Model\User")
     */
    protected $manager;

    /**
     * @var array|Group[]
     *
     * @Serializer\Expose()
     * @Serializer\Type("ArrayCollection<Tests\Fixtures\Model\Group>")
     */
    protected $groups = [];

    /**
     * @var array|User
     *
     * @Serializer\Expose()
     * @Serializer\Type("ArrayCollection<string,Tests\Fixtures\Model\User>")
     */
    protected $parents = [];

    /**
     * @var array|string[]
     *
     * @Serializer\Expose()
     * @Serializer\Type("array<string>")
     */
    protected $tags = [];

    /**
     * @var array|string[]
     *
     * @Serializer\Expose()
     * @Serializer\Type("array<string,string>")
     */
    protected $settings = [];

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
     * @Serializer\Type("string")
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    /**
     * @return User|null
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param User|null $manager
     *
     * @return User
     */
    public function setManager($manager): User
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return array|Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param array|Group[] $groups
     *
     * @return User
     */
    public function setGroups($groups): User
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @return array|\string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array|\string[] $tags
     *
     * @return User
     */
    public function setTags($tags): User
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return array|\string[]
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array|\string[] $settings
     *
     * @return User
     */
    public function setSettings($settings): User
    {
        $this->settings = $settings;

        return $this;
    }
}