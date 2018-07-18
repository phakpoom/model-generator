<?php

namespace Bonn\Generator\Model\Type;

use Bonn\Generator\Storage\CodeGeneratedStorageInterface;

interface DoctrineMappingInterface
{
    /**
     * @param \SimpleXMLElement $XMLElement
     * @param CodeGeneratedStorageInterface $storage
     * @param array $options
     */
    public function map(\SimpleXMLElement $XMLElement, CodeGeneratedStorageInterface $storage, array $options);
}
