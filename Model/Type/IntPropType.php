<?php

namespace Bonn\Generator\Model\Type;

use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Nette\PhpGenerator\ClassType;

class IntPropType implements PropTypeInterface, DoctrineMappingInterface
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
    public static function create(string $name, ?string $defaultValue = null): PropTypeInterface
    {
        return new self($name, $defaultValue);
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'int';
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
            $prop->setComment("\n@var int\n");

            return;
        }

        $prop->setComment("\n@var int|null\n");
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
        $method->setReturnType('int');
        $method
            ->setBody('return $this->' . $this->name . ';');

        if ($isNullable) {
            $method->setComment("\n@return int|null\n");

            return;
        }

        $method->setComment("\n@return int\n");
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
            ->setTypeHint('int');
        ;

        if ($isNullable) {
            $method->setComment("\n@param int|null $$this->name \n");

            return;
        }

        $method->setComment("\n@param int $$this->name\n");
    }

    /**
     * {@inheritdoc}
     */
    public function map(\SimpleXMLElement $XMLElement, CodeGeneratedStorageInterface $storage, array $options)
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'integer');
    }

    /**
     * {@inheritdoc}
     */
    public function getUses(): array
    {
        return [];
    }

    /**
     * @param string|null $v
     * @return int|null
     */
    private function convertDefaultValue(?string $v)
    {
        return null !== $v ? (int) $v : null;
    }
}
