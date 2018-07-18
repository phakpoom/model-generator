<?php

namespace Bonn\Generator\Transformer;

use Bonn\Generator\Code;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class XmlTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($XMLElement, array $options): Code
    {
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($XMLElement->asXML());

        return new Code($dom->saveXML());
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $options): bool
    {
        return $data instanceof \SimpleXMLElement;
    }

    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
    }
}
