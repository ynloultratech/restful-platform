<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema\Model\Describer;

use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\SymfonyValidatorDescriber;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;

class SymfonyValidatorDescriberTest extends TestCase
{
    public function testDescribe()
    {
        $class = new class
        {
            /**
             * @Assert\NotBlank()
             */
            protected $name;

            /**
             * @Assert\NotNull()
             */
            protected $age;

            protected $nick;
        };

        //NotBlank
        $propertySchema = new ModelPropertySchema('name');
        $ref = new \ReflectionProperty($class, 'name');
        $context = new DescribeContext($ref);
        (new SymfonyValidatorDescriber())->describe($propertySchema, $context);
        self::assertTrue($propertySchema->isRequired());

        //NotNull
        $propertySchema = new ModelPropertySchema('age');
        $ref = new \ReflectionProperty($class, 'age');
        $context = new DescribeContext($ref);
        (new SymfonyValidatorDescriber())->describe($propertySchema, $context);
        self::assertTrue($propertySchema->isRequired());

        //AllowNull
        $propertySchema = new ModelPropertySchema('nick');
        $ref = new \ReflectionProperty($class, 'nick');
        $context = new DescribeContext($ref);
        (new SymfonyValidatorDescriber())->describe($propertySchema, $context);
        self::assertFalse($propertySchema->isRequired());
    }

    public function testSupports()
    {
        $class = new class
        {
            protected $string;
        };
        $propertySchema = new ModelPropertySchema('string');
        $ref = new \ReflectionProperty($class, 'string');
        $context = new DescribeContext($ref);
        self::assertTrue((new SymfonyValidatorDescriber())->supports($propertySchema, $context));

        $context = new DescribeContext(new VirtualPropertyMetadata($class, 'string'));
        self::assertFalse((new SymfonyValidatorDescriber())->supports($propertySchema, $context));
    }
}
