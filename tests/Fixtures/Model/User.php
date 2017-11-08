<?php

namespace Tests\Fixtures\Model;

use JMS\Serializer\Annotation as Serializer;

class User
{
    /**
     * @var string
     *
     * @Serializer\SerializedName("username")
     * @Serializer\Type("string")
     */
    protected $username;

    /**
     * @var string
     *
     * @Serializer\SerializedName("first_name")
     * @Serializer\Type("string")
     *
     * @Serializer\Groups({"public"})
     */
    protected $firstName;

    /**
     * @var string
     *
     * @Serializer\SerializedName("last_name")
     * @Serializer\Type("string")
     *
     * @Serializer\Groups({"public"})
     */
    protected $lastName;
}