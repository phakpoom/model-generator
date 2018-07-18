<?php

namespace Bonn\Generator\Sylius;

use Bonn\Generator\NameResolver;
use Nette\PhpGenerator\PhpNamespace;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class FactoryGenerator extends AbstractSyliusGenerator
{
    public function generate(array $options)
    {
        $options = $this->optionResolver->resolve($options);

        $class = $options['class'];
        $this->ensureClassExists($class);

        $classNamespace = new PhpNamespace($this->getFactoryNameSpace($class));
        $interfaceNamespace = new PhpNamespace($this->getFactoryNameSpace($class));
        $className = NameResolver::resolveOnlyClassName($class);

        $factoryClass = $classNamespace->addClass($className. 'Factory');

        $classNamespace->addUse(FactoryInterface::class);
        $classNamespace->addUse($class . 'Interface');
        $factoryClass->addProperty('className')->setComment("\n @var string \n");

        $factoryClass->addMethod('__construct')
            ->setVisibility('public')->setBody('$this->className = $className;')
            ->addParameter('className')->setTypeHint('string');

        $factoryClass->addMethod('createNew')
            ->setVisibility('public')->setBody('return new $this->className();')
            ->setComment("\n @var " . NameResolver::resolveOnlyInterfaceName($class) . " \n");

        $factoryInterfaceClass = $interfaceNamespace->addInterface($className. 'FactoryInterface');
        $interfaceNamespace->addUse(FactoryInterface::class);
        $factoryInterfaceClass->addExtend(FactoryInterface::class);

        $this->storage->add($factoryClass, $factoryClass->getName());
        $this->storage->add($factoryInterfaceClass, $factoryInterfaceClass->getName());
    }

    /**
     * @param string $modelClass
     * @return mixed
     */
    public static function getFactoryNameSpace(string $modelClass)
    {
        $namespace = NameResolver::resolveNamespace($modelClass);
        return str_replace('Model', 'Factory', $namespace);
    }
}
