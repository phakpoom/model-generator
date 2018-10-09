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

final class DoctrineMappingGenerator extends AbstractGenerator implements GeneratorInterface
{
    public function __construct(CodeGeneratedStorageInterface $storage, StringToPropTypeConverterInterface $propTypeConverter)
    {
        parent::__construct($storage, $propTypeConverter);

        $this->optionResolver->setRequired('doctrine_resource_mapping_dir');
    }

    /**
     * @param array $options
     */
    public function generate($options = [])
    {
        $options = $this->optionResolver->resolve($options);
        $fullClassName = $options['class'];
        $info = $options['info'];

        $props = [];

        $onlyClassName = NameResolver::resolveOnlyClassName($fullClassName);

        if (!empty($info)) {
            $props = $this->propTypeConverter->convertMultiple($info);
        }

        $root = self::createDoctrineMappingXml();
        $mappedSuper = $root->addChild('mapped-superclass');
        $mappedSuper->addAttribute('name', $fullClassName);
        $mappedSuper->addAttribute('table', strtolower(explode('\\', $fullClassName)[0]) . '_' . NameResolver::camelToUnderScore($onlyClassName));

        $id = $mappedSuper->addChild('id');
        $id->addAttribute('name', 'id');
        $id->addAttribute('type', 'integer');
        $id->addChild('generator')->addAttribute('strategy', 'AUTO');

        // Extension
        if ($options['with_timestamp_able']) {
            $root->addAttribute('xmlns:xmlns:gedmo', 'http://gediminasm.org/schemas/orm/doctrine-extensions-mapping');

            $field = $mappedSuper->addChild('field');
            $field->addAttribute('name', 'createdAt');
            $field->addAttribute('type', 'datetime');
            $field->addChild('xmlns:gedmo:timestampable')->addAttribute('on', 'create');

            $field = $mappedSuper->addChild('field');
            $field->addAttribute('name', 'updatedAt');
            $field->addAttribute('type', 'datetime');
            $field->addAttribute('nullable', 'true');
            $field->addChild('xmlns:gedmo:timestampable')->addAttribute('on', 'update');
        }
        if ($options['with_code']) {
            $field = $mappedSuper->addChild('field');
            $field->addAttribute('name', 'code');
            $field->addAttribute('type', 'string');
            $field->addAttribute('length', '20');
            $field->addAttribute('unique', 'true');
            $field->addAttribute('nullable', 'false');
        }
        if ($options['with_toggle']) {
            $field = $mappedSuper->addChild('field');
            $field->addAttribute('name', 'enabled');
            $field->addAttribute('type', 'boolean');
        }

        if (!empty($props)) {
            /** @var PropTypeInterface $prop */
            foreach ($props as $prop) {
                if ($prop instanceof DoctrineMappingInterface) {
                    $prop->map($mappedSuper, $this->getStorage(), $options);
                }
            }
        }

        $this->storage->add($root, $onlyClassName.'.orm.xml', $options['doctrine_resource_mapping_dir'] . $onlyClassName .'.orm.xml');
    }

    /**
     * @return \SimpleXMLElement
     */
    public static function createDoctrineMappingXml(): \SimpleXMLElement
    {
        $doctrineMapping = new \SimpleXMLElement('<doctrine-mapping />');
        $doctrineMapping->addAttribute('xmlns', 'http://doctrine-project.org/schemas/orm/doctrine-mapping');
        $doctrineMapping->addAttribute("xmlns:xmlns:xsi", 'http://www.w3.org/2001/XMLSchema-instance');
        $doctrineMapping->addAttribute("xmlns:xsi:schemaLocation", "http://doctrine-project.org/schemas/orm/doctrine-mapping http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd");

        return $doctrineMapping;
    }
}
