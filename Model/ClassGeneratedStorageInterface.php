<?php

namespace Bonn\Generator\Model;

use Nette\PhpGenerator\ClassType;

interface ClassGeneratedStorageInterface
{
    /**
     * @return array|ClassType[]
     */
    public function getClasses();

    /**
     * @return array|ClassType[]
     */
    public function getInterfaces();

    /**
     * @param ClassType $classType
     */
    public function addClasses(ClassType $classType);

    /**
     * @param ClassType $classType
     */
    public function addInterfaces(ClassType $classType);
}
