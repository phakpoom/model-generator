<?php

namespace Bonn\Generator\Model\Converter;

use Bonn\Generator\Model\Type\PropTypeInterface;

interface StringToPropTypeConverterInterface
{
    /**
     * @return array
     */
    public function getSupportedType() :array;

    /**
     * @param string $infosString
     * @return array|PropTypeInterface[]
     */
    public function convertMultiple(string $infosString): array;

    /**
     * @param string $string
     *
     * @return PropTypeInterface
     * @throws \InvalidArgumentException
     */
    public function convert(string $string): PropTypeInterface;
}
