<?php

namespace Bonn\Generator\Transformer;

use Bonn\Generator\Code;
use Nette\PhpGenerator\ClassType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterfaceTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($interface, array $options): Code
    {
        /** @var $interface ClassType */
        $interface->setProperties([]);
        foreach ($interface->getMethods() as $method) {
            $method->setBody(null);
        }

        $dir = null;
        if ($options['class_start_dir']) {
            $dir = $options['class_start_dir'] . str_replace('\\', '/', $interface->getNamespace()->getName()) . '/' . $interface->getName() . '.php';
        }

        return new Code(RenderWithDoctype::render((string) $interface->getNamespace()), $dir);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $options): bool
    {
        return $data instanceof ClassType && $data->getType() === ClassType::TYPE_INTERFACE;
    }

    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class_start_dir' => null
        ]);
    }
}
