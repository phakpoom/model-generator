<?php

namespace Bonn\Generator\Sylius;

use Bonn\Generator\NameResolver;
use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Symfony\Component\Yaml\Yaml;

final class GridGenerator extends AbstractSyliusGenerator
{
    public function __construct(CodeGeneratedStorageInterface $storage)
    {
        parent::__construct($storage);

        $this->optionResolver->setDefaults([
            'with_repo' => false,
        ]);
    }

    public function generate(array $options)
    {
        $options = $this->optionResolver->resolve($options);

        $class = $options['class'];
        $this->ensureClassExists($class);

        $className = NameResolver::camelToUnderScore(NameResolver::resolveOnlyClassName($class));

        $arr = [];
        $rootKey = 'sylius_grid';
        $gridArr = [];
        $gridArr['driver']['name'] = "doctrine/orm";
        $gridArr['driver']['options'] = [
            'class' => '%' . sprintf('%s.model.%s.class', $options['resource_name'], NameResolver::camelToUnderScore($className)) . '%',
        ];

        if ($options['with_repo']) {
            $gridArr['driver']['options']['repository']['method'] = 'createAdminListQueryBuilder';
            $gridArr['driver']['options']['repository']['arguments'] = null;
        }

        $gridArr['sorting'] = null;

        $gridArr['filters'] = [
            'keyword' => [
                'type' => 'string',
                'label' => 'search',
                'options' => ['fields' => null],
            ]
        ];

        $reflectedClass = new \ReflectionClass($class);
        foreach ($reflectedClass->getProperties() as $property) {
            $isString = false !== strpos($property->getDocComment(), 'string');
            $isBool = false !== strpos($property->getDocComment(), 'bool');
            if (!$isString && !$isBool) {
                continue;
            }

            $gridArr['fields'][$property->getName()]['type'] = 'twig';
            if ($isString) {
                $gridArr['fields'][$property->getName()]['type'] = 'string';
            }

            $gridArr['fields'][$property->getName()]['label'] = $options['resource_name'] . '.' . NameResolver::camelToUnderScore($className) . '.' . NameResolver::camelToUnderScore($property->getName());

            if ($isBool) {
                $gridArr['fields'][$property->getName()]['options'] = [
                    'template' => '@'
                ];
            }
        }

        $gridArr['actions'] = [
            'main' => [
                'create' => [
                    'type' => 'create'
                ]
            ],
            'item' => [
                'update' => [
                    'type' => 'update'
                ],
                'delete' => [
                    'type' => 'delete'
                ]
            ]
        ];

        $arr[$rootKey]['grids'][$options['resource_name'] . '_admin_' . $className] = $gridArr;

        $this->storage->add($arr, 'routing', $options['resource_dir'] . 'config/grid/admin/' . NameResolver::camelToUnderScore($className) .'.yml');
    }
}
