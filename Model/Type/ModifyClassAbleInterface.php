<?php

namespace Bonn\Generator\Model\Type;

use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Nette\PhpGenerator\ClassType;

interface ModifyClassAbleInterface
{
    /**
     * @param ClassType $classType
     * @param CodeGeneratedStorageInterface $storage
     * @return mixed
     */
    public function modify(ClassType $classType, CodeGeneratedStorageInterface $storage);
}
