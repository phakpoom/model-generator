<?php

namespace Bonn\Generator\Sylius;

use Bonn\Generator\NameResolver;
use Nette\PhpGenerator\PhpNamespace;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class RepositoryGenerator extends AbstractSyliusGenerator
{
    public function generate(array $options)
    {
        $options = $this->optionResolver->resolve($options);

        $class = $options['class'];
        $this->ensureClassExists($class);

        $classNamespace = new PhpNamespace($this->getRepositoryNameSpace($class));
        $interfaceNamespace = new PhpNamespace($this->getRepositoryNameSpace($class));
        $className = NameResolver::resolveOnlyClassName($class);

        $repositoryClass = $classNamespace->addClass($className. 'Repository');

        $classNamespace->addUse(EntityRepository::class);
        $repositoryClass->addExtend(EntityRepository::class);

        $repositoryInterfaceClass = $interfaceNamespace->addInterface($className. 'RepositoryInterface');
        $interfaceNamespace->addUse(RepositoryInterface::class);
        $repositoryInterfaceClass->addExtend(RepositoryInterface::class);

        $this->storage->add($repositoryClass, $repositoryClass->getName());
        $this->storage->add($repositoryInterfaceClass, $repositoryInterfaceClass->getName());
    }

    /**
     * @param string $modelClass
     * @return mixed
     */
    public static function getRepositoryNameSpace(string $modelClass)
    {
        $namespace = NameResolver::resolveNamespace($modelClass);
        return str_replace('Model', 'Doctrine\\ORM', $namespace);
    }
}
