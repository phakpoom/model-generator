<?php

namespace Bonn\Generator\Model;

use Bonn\Generator\Storage\CodeGeneratedStorageInterface;

interface GeneratorInterface
{
    public function generate();

    /**
     * @return CodeGeneratedStorageInterface
     */
    public function getStorage(): CodeGeneratedStorageInterface;
}
