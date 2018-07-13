<?php

namespace Bonn\Generator\Model\Type;

use Nette\PhpGenerator\Method;

interface ConstructorAwareInterface
{
    /**
     * @param Method $method
     * @return mixed
     */
    public function addConstructor(Method $method);
}
