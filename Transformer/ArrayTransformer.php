<?php

namespace Bonn\Generator\Transformer;

use Bonn\Generator\Code;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class ArrayTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($data, array $options): Code
    {
        return new Code(Yaml::dump($data, 10));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $options): bool
    {
        return is_array($data);
    }

    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
    }
}
