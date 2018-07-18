<?php

namespace Bonn\Generator\Sylius;

use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractSyliusGenerator
{
    /**
     * @var OptionsResolver
     */
    protected $optionResolver;

    /**
     * @var CodeGeneratedStorageInterface
     */
    protected $storage;

    public function __construct(CodeGeneratedStorageInterface $storage)
    {
        $this->storage = $storage;
        $this->optionResolver = new OptionsResolver();

        $this->optionResolver->setDefaults([
            'class' => null,
            'resource_name' => null,
            'resource_dir' => null
        ]);

        $this->optionResolver
            ->setRequired('class')
            ->setRequired('resource_name')
            ->setRequired('resource_dir')
        ;
    }

    /**
     * @param string $class
     */
    protected function ensureClassExists(string $class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf("Class %s do not exists", $class));
        }
    }
}
