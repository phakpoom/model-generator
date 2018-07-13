<?php

declare(strict_types=1);

namespace Bonn\Generator\Model\Type;

use Bonn\Generator\Model\ClassGeneratedStorageInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class TranslationPropType implements PropTypeInterface, ModifyClassAbleInterface
{
    private $name;
    private $defaultValue;

    protected function __construct(string $name, ?string $defaultValue = null)
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
        return 'translation';
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public');

        $method->setReturnNullable(true);
        $method->setReturnType('string');
        $method
            ->setBody('return $this->getTranslation()->get' . ucfirst($this->name) . '();')
            ->setComment("\n@return null|string\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setVisibility('public')
            ->setBody('$this->getTranslation()->set' . ucfirst($this->name) . "($$this->name);");

        $method->setReturnType('void');

        $method
            ->addParameter($this->name)
            ->setNullable(true)
            ->setTypeHint('string');

        $method->setComment("\n@param null|string $$this->name \n");

        if ($classType->getType() === ClassType::TYPE_CLASS) {
            $method = $classType
                ->addMethod('createTranslation')
                ->setVisibility('protected')
                ->setReturnType(TranslationInterface::class);

            $method->setBody('return new ' . $classType->getName() . 'Translation();');
            $method->setComment("\n @return " . $this->getTranslationInterfaceName($classType) . "\n");
        }
    }

    /**
     * @param ClassType $classType
     * @param bool $isFull
     * @return string
     */
    private function getTranslationInterfaceName(ClassType $classType, $isFull = false)
    {
        $className = str_replace('Interface', '', $classType->getName());
        $className = $className . 'TranslationInterface';
        return $isFull ? $classType->getNamespace()->getName() . '\\' . $className : $className;
    }

    /**
     * @param ClassType $classType
     * @param bool $isFull
     * @return string
     */
    private function getTranslationClassName(ClassType $classType, $isFull = false)
    {
        $className = $classType->getName() . 'Translation';
        return $isFull ? $classType->getNamespace()->getName() . '\\' . $className : $className;
    }

    /**
     * {@inheritdoc}
     */
    public function getUses(): array
    {
        return [

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function modify(ClassType $classType, ClassGeneratedStorageInterface $storage)
    {
        if ($classType->getType() === ClassType::TYPE_CLASS) {
            $translationClass = null;
            if (isset($storage->getClasses()[$this->getTranslationClassName($classType)])) {
                $translationClass = $storage->getClasses()[$this->getTranslationClassName($classType)];
            }

            if (null === $translationClass) {
                $classType->addTrait(TranslatableTrait::class, [' __construct as protected initializeTranslationsCollection']);
                $classType->getNamespace()->addUse(TranslatableTrait::class);
                $classType->getNamespace()->addUse(TranslationInterface::class);
                $classType->addComment("@method " . $this->getTranslationInterfaceName($classType) . " getTranslation()");
                $classType->getMethod('__construct')->addBody('$this->initializeTranslationsCollection();');

                $classType->getNamespace()->addUse($this->getTranslationClassName($classType, true));
                $classNamespace = new PhpNamespace($classType->getNamespace()->getName());
                $classNamespace->addUse(AbstractTranslation::class);
                $translationClass = $classNamespace->addClass($this->getTranslationClassName($classType));
                $translationClass->addExtend(AbstractTranslation::class);
                $translationClass->addExtend(ResourceInterface::class);
                $propType = IntPropType::create('id');

                $propType->addGetter($translationClass);
                $propType->addSetter($translationClass);

                $storage->addClasses($translationClass);
            }

            $propType = StringPropType::create($this->name);
            $propType->addProperty($translationClass);
            $propType->addGetter($translationClass);
            $propType->addSetter($translationClass);
        } elseif ($classType->getType() === ClassType::TYPE_INTERFACE) {
            $translationInterfaceClass = null;
            if (isset($storage->getInterfaces()[$this->getTranslationInterfaceName($classType)])) {
                $translationInterfaceClass = $storage->getInterfaces()[$this->getTranslationInterfaceName($classType)];
            }

            if (null === $translationInterfaceClass) {
                $classType->getNamespace()->addUse(TranslatableInterface::class);
                $classType->addExtend(TranslatableInterface::class);

                $interfaceNamespace = new PhpNamespace($classType->getNamespace()->getName());
                $interfaceNamespace->addUse(TranslationInterface::class);
                $translationInterfaceClass = $interfaceNamespace->addInterface($this->getTranslationInterfaceName($classType));
                $translationInterfaceClass->addExtend(TranslationInterface::class);

                $storage->addInterfaces($translationInterfaceClass);
            }

            $propType = StringPropType::create($this->name);

            $propType->addGetter($translationInterfaceClass);
            $propType->addSetter($translationInterfaceClass);
        }
    }
}
