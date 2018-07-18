<?php

namespace Bonn\Generator\Sylius;

use Bonn\Generator\NameResolver;
use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

final class SyliusResourceGenerator extends AbstractSyliusGenerator
{
    public function __construct(CodeGeneratedStorageInterface $storage)
    {
        parent::__construct($storage);

        $this->optionResolver->setDefaults([
            'with_factory' => false,
            'with_form' => false,
            'with_repo' => false,
        ]);
    }

    /**
     * @param array $options
     */
    public function generate(array $options)
    {
        $options = $this->optionResolver->resolve($options);

        $class = $options['class'];
        $this->ensureClassExists($class);

        $className = NameResolver::resolveOnlyClassName($class);
        $reflection = new \ReflectionClass($class);

        $resourceName = $options['resource_name'] . '.' . NameResolver::camelToUnderScore($className);

        $arr = [];
        $arr['sylius_resource']['resources'][$resourceName] = [];

        $resourceArr = &$arr['sylius_resource']['resources'][$resourceName];

        $resourceArr['classes']['model'] = $class;
        $resourceArr['classes']['interface'] = $class . 'Interface';

        if ($options['with_factory']) {
            $resourceArr['classes']['factory'] = FactoryGenerator::getFactoryNameSpace($class) . '\\' . $className . 'Factory';
        }
        if ($options['with_form']) {
            $resourceArr['classes']['form'] = FormGenerator::getFormTypeNameSpace($class) . '\\' . $className . 'Type';
        }
        if ($options['with_repo']) {
            $resourceArr['classes']['repository'] = RepositoryGenerator::getRepositoryNameSpace($class) . '\\' . $className . 'Repository';
        }

        if (in_array(TranslatableInterface::class, $reflection->getInterfaceNames())) {
            $resourceArr['translation']['classes']['model'] = $class . 'Translation';
            $resourceArr['translation']['classes']['interface'] = $class . 'TranslationInterface';
        }

        $this->storage->add($arr, 'sylius_resource', $options['resource_dir'] . 'config/app/sylius_resource/' . NameResolver::camelToUnderScore($className) .'.yml');
    }
}
