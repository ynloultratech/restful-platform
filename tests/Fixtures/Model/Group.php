<?php


namespace Tests\Fixtures\Model;


use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("none")
 * @Serializer\AccessorOrder(order="alphabetical")
 */
class Group
{
    /**
     * @var string
     *
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     */
    protected $name;

    /**
     * @var array
     *
     * @Serializer\Exclude()
     */
    protected $roles = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("backendAllowed")
     * @Serializer\Type("boolean")
     *
     * @return bool
     */
    protected function isBackendAllowed()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }
}