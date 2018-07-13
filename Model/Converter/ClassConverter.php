<?php

namespace Bonn\Generator\Model\Converter;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Helpers;

final class ClassConverter implements ClassConverterInterface
{
    /**
     * @param ClassType $class
     * @return string
     */
    public function getClassAsString(ClassType $class): string
    {
        $class->addImplement($class->getNamespace()->getName() . '\\' . $class->getName() . 'Interface');

        foreach ($class->getMethods() as $method) {
            $method->setComment("\n{@inheritdoc}\n");
        }

        return $this->_renderWithPhpTagAndScrictType((string)$class->getNamespace());
    }

    /**
     * @param ClassType $interface
     * @return string
     */
    public function getInterfaceAsString(ClassType $interface): string
    {
        $interface->setProperties([]);
        foreach ($interface->getMethods() as $method) {
            $method->setBody(null);
        }

        return $this->_renderWithPhpTagAndScrictType((string) $interface->getNamespace());
    }

    /**
     * @param string $content
     * @return mixed
     */
    private function _renderWithPhpTagAndScrictType(string $content)
    {
        return str_replace("\n\n\n", "\n\n", Helpers::tabsToSpaces("<?php\n\ndeclare(strict_types=1);\n\n" . $content));
    }
}
