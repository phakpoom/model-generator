<?php

namespace Bonn\Generator\Model\Type;

use Nette\PhpGenerator\ClassType;

interface PropTypeInterface
{
    /**
     * @param string $name
     * @param null|string|null $defaultValue
     * @return PropTypeInterface
     */
    public static function create(string $name, ?string $defaultValue = null): PropTypeInterface;

    /**
     * @return string
     */
    public static function getTypeName(): string;

    /**
     * @param ClassType $classType
     * @return mixed
     */
    public function addProperty(ClassType $classType);

    /**
     * @param ClassType $classType
     * @return mixed
     */
    public function addGetter(ClassType $classType);

    /**
     * @param ClassType $classType
     * @return mixed
     */
    public function addSetter(ClassType $classType);

    /**
     * @return array
     */
    public function getUses(): array;
}
