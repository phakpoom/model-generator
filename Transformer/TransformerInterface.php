<?php

namespace Bonn\Generator\Transformer;

use Bonn\Generator\Code;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface TransformerInterface
{
    /**
     * @param $data
     * @param array $options
     * @return Code
     */
    public function transform($data, array $options): Code;

    /**
     * @param $data
     * @param array $options
     * @return bool
     */
    public function supports($data, array $options): bool;

    /**
     * @param OptionsResolver $resolver
     */
    public function configurationOptions(OptionsResolver $resolver);
}
