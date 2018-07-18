<?php

namespace Bonn\Generator\Sylius;

use Bonn\Generator\NameResolver;
use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Symfony\Component\Yaml\Yaml;

final class RoutingGenerator extends AbstractSyliusGenerator
{
    public function __construct(CodeGeneratedStorageInterface $storage)
    {
        parent::__construct($storage);

        $this->optionResolver->setDefaults([
            'with_grid' => false,
        ]);
    }

    public function generate(array $options)
    {
        $options = $this->optionResolver->resolve($options);

        $class = $options['class'];
        $this->ensureClassExists($class);

        $className = NameResolver::camelToUnderScore(NameResolver::resolveOnlyClassName($class));

        $arr = [];
        $rootKey = $options['resource_name'] . '_admin_' . $className;
        $arr[$rootKey]['resource'] = "alias: " . $options['resource_name'] . '.' . $className;
        $arr[$rootKey]['resource'] .= "\nsection: admin";
        $arr[$rootKey]['resource'] .= "\ntemplates: ~";
        $arr[$rootKey]['resource'] .= "\nredirect: index";
        $arr[$rootKey]['resource'] .= "\nexcept: ['show']";
        $grid = '~';
        if ($options['with_grid']) {
            $grid = $options['resource_name'] . '_admin_' . $className;
        }

        $arr[$rootKey]['resource'] .= "\ngrid: $grid";
        $arr[$rootKey]['resource'] .= "\nform:\n   type: ~";
        $arr[$rootKey]['resource'] .= "\nvars:\n   all:\n       templates:\n           form: ~";
        $arr[$rootKey]['resource'] .= "\n   index: ~";
        $arr[$rootKey]['type'] = 'sylius.resource';

        $arr = Yaml::dump($arr, 2, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        $this->storage->add($arr, 'routing', $options['resource_dir'] . 'config/routing/admin/' . NameResolver::camelToUnderScore($className) .'.yml');
    }
}
