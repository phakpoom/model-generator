<?php

namespace Bonn\Generator\Model\Type;

use Nette\PhpGenerator\ClassType;

class StringPropType implements PropTypeInterface
{
    private $name;
    private $defaultValue;

    private function __construct(string $name, ?string $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = $defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(string $name, ?string $defaultValue = null): PropTypeInterface
    {
        return new self($name, $defaultValue);
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'string';
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType)
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('private');

        $prop->setValue($this->defaultValue);
        if (null !== $this->defaultValue) {
            $prop->setComment("\n@var string\n");

            return;
        }

        $prop->setComment("\n@var null|string\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public')
        ;

        $isNullable = null === $this->defaultValue;

        $method->setReturnNullable($isNullable);
        $method->setReturnType('string');
        $method
            ->setBody('return $this->' . $this->name . ';');

        if ($isNullable) {
            $method->setComment("\n@return null|string\n");

            return;
        }

        $method->setComment("\n@return string\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setVisibility('public')
            ->setBody('$this->' . $this->name . ' = $' . $this->name . ';');

        $method->setReturnType('void');

        $isNullable = null === $this->defaultValue;

        $method
            ->addParameter($this->name)
            ->setNullable($isNullable)
            ->setTypeHint('string');
        ;

        if ($isNullable) {
            $method->setComment("\n@param null|string $$this->name \n");

            return;
        }

        $method->setComment("\n@param string $$this->name\n");
    }

    /**
     * {@inheritdoc}
     */
    public function getUses(): array
    {
        return [];
    }
}
