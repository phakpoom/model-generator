<?php

namespace Bonn\Generator\Model\Type;

use Bonn\Generator\Model\NameResolver;
use Nette\PhpGenerator\ClassType;

class InterfacePropType implements PropTypeInterface
{
    private $name;
    private $fullInterfaceName;
    private $interfaceName;

    private function __construct(string $name, string $interfaceName)
    {
        $this->name = $name;
        $this->fullInterfaceName = $interfaceName;
        $this->interfaceName = NameResolver::resolveOnlyInterfaceName($this->fullInterfaceName);
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'interface';
    }

    /**
     * {@inheritdoc}
     */
    public static function create(string $name, ?string $interfaceName = null): PropTypeInterface
    {
        return new self($name, $interfaceName);
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType)
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('private');

        $prop->setComment("\n@var null|$this->interfaceName\n");
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

        $method->setComment("\n@return null|$this->interfaceName\n");

        $method->setReturnType($this->fullInterfaceName);

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

        $method->setComment("\n@param null|$this->interfaceName $$this->name \n");
        $parameter->setTypeHint($this->fullInterfaceName);
    }

    /**
     * {@inheritdoc}
     */
    public function getUses(): array
    {
        return [
            $this->fullInterfaceName
        ];
    }
}
