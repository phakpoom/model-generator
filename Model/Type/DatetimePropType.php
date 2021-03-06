<?php

namespace Bonn\Generator\Model\Type;

use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Nette\PhpGenerator\ClassType;

class DatetimePropType implements PropTypeInterface, DoctrineMappingInterface
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
    public static function getTypeName(): string
    {
        return 'datetime';
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
    public function addProperty(ClassType $classType)
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');

        $prop->setValue($this->defaultValue);
        $prop->setComment("\n@var \\Datetime|null\n");
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

        $method->setReturnNullable(true);

        $method->setComment("\n@return \\Datetime|null\n");

        $method->setReturnType('Datetime');

        $method
            ->setBody('return $this->' . $this->name . ';');
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

        $parameter = $method
            ->addParameter($this->name)
            ->setNullable(true)
        ;

        $method->setComment("\n@param \\Datetime|null $$this->name \n");
        $parameter->setTypeHint('Datetime');
    }

    /**
     * {@inheritdoc}
     */
    public function map(\SimpleXMLElement $XMLElement, CodeGeneratedStorageInterface $storage, array $options)
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'datetime');
    }

    /**
     * {@inheritdoc}
     */
    public function getUses(): array
    {
        return [];
    }
}
