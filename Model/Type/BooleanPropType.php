<?php

namespace Bonn\Generator\Model\Type;

use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Nette\PhpGenerator\ClassType;

class BooleanPropType implements PropTypeInterface, DoctrineMappingInterface
{
    private $name;
    private $defaultValue;

    private function __construct(string $name, ?string $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = $this->convertDefaultValue($defaultValue);
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'boolean';
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
        $prop->setComment("\n@var bool\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('is' . ucfirst($this->name))
            ->setVisibility('public')
        ;

        $method->setReturnNullable(false);

        $method->setComment("\n@return bool\n");

        $method->setReturnType('bool');

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
            ->setNullable(false)
        ;

        $method->setComment("\n@param bool $$this->name \n");
        $parameter->setTypeHint('bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getUses(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function map(\SimpleXMLElement $XMLElement, CodeGeneratedStorageInterface $storage, array $options)
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'boolean');
    }

    /**
     * @param string|null $v
     * @return bool
     */
    private function convertDefaultValue(?string $v)
    {
        return 'true' === $v;
    }
}
