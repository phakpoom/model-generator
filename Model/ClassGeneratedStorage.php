<?php

namespace Bonn\Generator\Model;

use Nette\PhpGenerator\ClassType;

final class ClassGeneratedStorage implements ClassGeneratedStorageInterface
{
    /**
     * @var ClassType[]|array
     */
    private $interfaceClass = [];

    /**
     * @var ClassType[]|array
     */
    private $modelClass = [];

    /**
     * @return array|ClassType[]
     */
    public function getClasses()
    {
        return $this->modelClass;
    }

    /**
     * @return array|ClassType[]
     */
    public function getInterfaces()
    {
        return $this->interfaceClass;
    }

    public function addClasses(ClassType $classType)
    {
        $this->modelClass[$classType->getName()] = $classType;
    }

    public function addInterfaces(ClassType $classType)
    {
        $this->interfaceClass[$classType->getName()] = $classType;
    }
}
