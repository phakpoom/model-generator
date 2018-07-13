<?php

namespace Bonn\Generator\Model\Type;

use Nette\PhpGenerator\ClassType;

class ArrayPropType implements PropTypeInterface
{
    private $name;
    private $defaultValue;

    private function __construct(string $name, ?string $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = $defaultValue ?: [];
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
        return 'array';
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
        $prop->setComment("\n@var array\n");
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

        $method->setReturnNullable(false);
        $method->setReturnType('array');
        $method
            ->setBody('return $this->' . $this->name . ';')
            ->setComment("\n@return array\n");
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

        $method
            ->addParameter($this->name)
            ->setNullable(false)
            ->setTypeHint('array');
        ;

        $method->setComment("\n@param array $$this->name \n");
    }

    /**
     * {@inheritdoc}
     */
    public function getUses(): array
    {
        return [];
    }
}
