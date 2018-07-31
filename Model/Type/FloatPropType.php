<?php

namespace Bonn\Generator\Model\Type;

use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Nette\PhpGenerator\ClassType;

class FloatPropType implements PropTypeInterface, DoctrineMappingInterface
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
        return 'float';
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
        if (null !== $this->defaultValue) {
            $prop->setComment("\n@var float\n");

            return;
        }

        $prop->setComment("\n@var null|float\n");
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
        $method->setReturnType('float');
        $method
            ->setBody('return $this->' . $this->name . ';');

        if ($isNullable) {
            $method->setComment("\n@return null|float\n");

            return;
        }

        $method->setComment("\n@return float\n");
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

        $isNullable = null === $this->defaultValue;

        $method
            ->addParameter($this->name)
            ->setNullable($isNullable)
            ->setTypeHint('float');
        ;

        if ($isNullable) {
            $method->setComment("\n@param null|float $$this->name \n");

            return;
        }

        $method->setComment("\n@param float $$this->name\n");
    }

    /**
     * {@inheritdoc}
     */
    public function map(\SimpleXMLElement $XMLElement, CodeGeneratedStorageInterface $storage, array $options)
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'float');
    }

    /**
     * {@inheritdoc}
     */
    public function getUses(): array
    {
        return [];
    }

    /**
     * @param null|string $v
     * @return float|null
     */
    private function convertDefaultValue(?string $v)
    {
        return null !== $v ? (float) $v : null;
    }
}
