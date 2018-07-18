<?php

namespace Bonn\Generator\Model;

use Bonn\Generator\Model\Converter\StringToPropTypeConverterInterface;
use Bonn\Generator\Model\Type\ConstructorAwareInterface;
use Bonn\Generator\Model\Type\DoctrineMappingInterface;
use Bonn\Generator\Model\Type\IntPropType;
use Bonn\Generator\Model\Type\ModifyClassAbleInterface;
use Bonn\Generator\Model\Type\PropTypeInterface;
use Bonn\Generator\Model\Type\StringPropType;
use Bonn\Generator\NameResolver;
use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ModelGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * @var array
     */
    private $classUses = [];

    /**
     * @var array
     */
    private $interfaceUses = [];

    public function __construct(CodeGeneratedStorageInterface $storage, StringToPropTypeConverterInterface $propTypeConverter)
    {
        parent::__construct($storage, $propTypeConverter);
    }

    /**
     * @param array $options
     */
    public function generate($options = [])
    {
        $options = $this->optionResolver->resolve($options);
        $fullClassName = $options['class'];
        $info = $options['info'];

        $namespace = NameResolver::resolveNamespace($fullClassName);

        $classNamespace = new PhpNamespace($namespace);

        $interfaceNamespace = new PhpNamespace($namespace);
        $onlyClassName = NameResolver::resolveOnlyClassName($fullClassName);

        $modelClass = $classNamespace->addClass($onlyClassName);
        $interfaceClass = $interfaceNamespace->addInterface($onlyClassName . 'Interface');

        $props = [];

        if (!empty($info)) {
            $props = $this->propTypeConverter->convertMultiple($info);
        }

        $constructMethod = $modelClass
            ->addMethod('__construct')
            ->setVisibility('public')
        ;

        $idPropType = IntPropType::create('id');
        $idPropType->addProperty($modelClass);
        $idPropType->addGetter($modelClass);

        // Extension
        if ($options['with_timestamp_able']) {
            $this->classUses[] = TimestampableTrait::class;
            $modelClass->addTrait(TimestampableTrait::class);
            $this->interfaceUses[] = TimestampableInterface::class;
            $interfaceClass->addExtend(TimestampableInterface::class);
        }
        if ($options['with_code']) {
            $codePropType = StringPropType::create('code');
            $codePropType->addProperty($modelClass);
            $codePropType->addGetter($modelClass);
            $codePropType->addSetter($modelClass);
            $this->interfaceUses[] = CodeAwareInterface::class;
            $interfaceClass->addExtend(CodeAwareInterface::class);
        }
        if ($options['with_toggle']) {
            $this->classUses[] = ToggleableTrait::class;
            $modelClass->addTrait(ToggleableTrait::class);
            $this->interfaceUses[] = ToggleableInterface::class;
            $interfaceClass->addExtend(ToggleableInterface::class);
        }

        if (!empty($props)) {
            /** @var PropTypeInterface $prop */
            foreach ($props as $prop) {
                $prop->addProperty($modelClass);
                $prop->addGetter($modelClass);
                $prop->addSetter($modelClass);
                $prop->addGetter($interfaceClass);
                $prop->addSetter($interfaceClass);

                if ($prop instanceof ConstructorAwareInterface) {
                    $prop->addConstructor($constructMethod);
                }

                if ($prop instanceof ModifyClassAbleInterface) {
                    $prop->modify($modelClass, $this->getStorage());
                    $prop->modify($interfaceClass, $this->getStorage());
                }

                $this->classUses = array_merge($this->classUses, $prop->getUses());
                $this->interfaceUses = array_merge($this->interfaceUses, $prop->getUses());
            }
        }

        $interfaceClass->getNamespace()->addUse(ResourceInterface::class);
        $interfaceClass->addExtend(ResourceInterface::class);

        foreach ($this->classUses as $use) {
            $classNamespace->addUse($use);
        }

        foreach ($this->interfaceUses as $use) {
            $interfaceNamespace->addUse($use);
        }

        $this->storage->add($modelClass, $modelClass->getName());
        $this->storage->add($interfaceClass, $interfaceClass->getName());
    }
}
