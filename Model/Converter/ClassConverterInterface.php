<?php

namespace Bonn\Generator\Model\Converter;

use Nette\PhpGenerator\ClassType;

interface ClassConverterInterface
{
    /**
     * @param ClassType $class
     * @return string
     */
    public function getClassAsString(ClassType $class): string;

    /**
     * @param ClassType $interface
     * @return string
     */
    public function getInterfaceAsString(ClassType $interface): string;
}
