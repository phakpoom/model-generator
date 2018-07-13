<?php

namespace Bonn\Generator\Model;

use Nette\PhpGenerator\ClassType;

interface ModelGeneratorInterface
{
    /**
     * @return array|ClassType[]
     */
    public function generate(): array;

    /**
     * @return ClassGeneratedStorageInterface
     */
    public function getStorage(): ClassGeneratedStorageInterface;
}
