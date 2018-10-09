<?php

namespace Bonn\Generator\Model\Type;

use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

interface ModifyClassAbleInterface
{
    /**
     * @param ClassType $classType
     * @param CodeGeneratedStorageInterface $storage
     * @param PhpNamespace $namespace
     *
     * @return mixed
     */
    public function modify(ClassType $classType, CodeGeneratedStorageInterface $storage, PhpNamespace $namespace);
}
