<?php

namespace Bonn\Generator\Model\Type;

use Bonn\Generator\Model\ClassGeneratedStorageInterface;
use Nette\PhpGenerator\ClassType;

interface ModifyClassAbleInterface
{
    /**
     * @param ClassType $classType
     * @param ClassGeneratedStorageInterface $storage
     * @return mixed
     */
    public function modify(ClassType $classType, ClassGeneratedStorageInterface $storage);
}
