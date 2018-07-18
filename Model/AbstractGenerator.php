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

abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * @var OptionsResolver
     */
    protected $optionResolver;

    /**
     * @var StringToPropTypeConverterInterface
     */
    protected $propTypeConverter;

    /**
     * @var CodeGeneratedStorageInterface
     */
    protected $storage;

    public function __construct(CodeGeneratedStorageInterface $storage, StringToPropTypeConverterInterface $propTypeConverter)
    {
        $this->propTypeConverter = $propTypeConverter;
        $this->storage = $storage;
        $this->optionResolver = new OptionsResolver();

        $this->optionResolver->setDefaults([
            'class' => null,
            'info' => '',
            'with_timestamp_able' => false,
            'with_code' => false,
            'with_toggle' => false,
        ]);

        $this->optionResolver
            ->setRequired('class')
            ->setRequired('info');
    }

    /**
     * @param array $options
     */
    abstract public function generate($options = []);

    /**
     * @return CodeGeneratedStorageInterface
     */
    public function getStorage(): CodeGeneratedStorageInterface
    {
        return $this->storage;
    }
}
