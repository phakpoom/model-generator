<?php

namespace Bonn\Generator\Transformer;

use Bonn\Generator\Code;
use Nette\PhpGenerator\ClassType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($class, array $options): Code
    {
        /** @var ClassType $class */
        if ($options['with_interface']) {
            $class->addImplement($class->getNamespace()->getName() . '\\' . $class->getName() . 'Interface');
        }

        foreach ($class->getMethods() as $method) {
            $method->setComment("\n{@inheritdoc}\n");
        }

        $dir = null;
        if ($options['class_start_dir']) {
            $dir = $options['class_start_dir'] . str_replace('\\', '/', $class->getNamespace()->getName()) . '/' . $class->getName() . '.php';
        }

        return new Code(RenderWithDoctype::render((string)$class->getNamespace()), $dir);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $options): bool
    {
        return $data instanceof ClassType && $data->getType() === ClassType::TYPE_CLASS;
    }

    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'with_interface' => true,
            'class_start_dir' => null
        ]);
    }
}
