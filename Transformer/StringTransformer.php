<?php

namespace Bonn\Generator\Transformer;

use Bonn\Generator\Code;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class StringTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($data, array $options): Code
    {
        return new Code($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $options): bool
    {
        return is_string($data);
    }

    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
    }
}
