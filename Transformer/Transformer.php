<?php

namespace Bonn\Generator\Transformer;

use BaconQrCode\Exception\InvalidArgumentException;
use Bonn\Generator\Code;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Transformer
{
    /**
     * @var array|TransformerInterface[]
     */
    private $transformers = [];

    public function __construct()
    {
        $this->transformers = [
            new ClassTransformer(),
            new InterfaceTransformer(),
            new StringTransformer(),
            new ArrayTransformer(),
            new XmlTransformer(),
        ];
    }

    /**
     * @param $data
     * @param string|null $outputPath
     * @param array $options
     * @return Code
     */
    public function transform($data, ?string $outputPath, array $options): Code
    {
        $optionResolver = new OptionsResolver();

        foreach ($this->transformers as $transformer) {
            $transformer->configurationOptions($optionResolver);
        }

        foreach ($this->transformers as $transformer) {
            $resolvedOptions = $optionResolver->resolve($options);
            if (!$transformer->supports($data, $resolvedOptions)) {
                continue;
            }

            $code = $transformer->transform($data, $resolvedOptions);

            if (null !== $outputPath) {
                $code->setOutputPath($outputPath);
            }

            return $code;
        }

        throw new InvalidArgumentException('No Transformer supports');
    }

    /**
     * @param TransformerInterface $class
     * @return Transformer
     */
    public function addTransformer(TransformerInterface $class): self
    {
        $this->transformers[] = $class;

        return $this;
    }
}
