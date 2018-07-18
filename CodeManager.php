<?php

namespace Bonn\Generator;

use Bonn\Generator\Storage\CodeGeneratedStorageInterface;
use Bonn\Generator\Transformer\Transformer;

class CodeManager
{
    /**
     * @var array|Code[]
     */
    private $codes = [];

    /**
     * @var CodeGeneratedStorageInterface
     */
    private $codeGeneratedStorage;

    /**
     * @var CodeWriterInterface
     */
    private $writer;

    public function __construct(CodeGeneratedStorageInterface $codeGeneratedStorage, ?CodeWriterInterface $writer = null)
    {
        $this->codeGeneratedStorage = $codeGeneratedStorage;
        $this->writer = $writer ?: new CodeWriter();
    }

    public function getCodes()
    {
        return $this->codes;
    }

    public function getStorage()
    {
        return $this->codeGeneratedStorage;
    }

    public function persist(array $options = [])
    {
        $transformer = new Transformer();
        $outputPaths = $this->codeGeneratedStorage->getOutputsPath();
        foreach ($this->codeGeneratedStorage->all() as $k => $item) {
            $this->codes[] = $transformer->transform($item, $outputPaths[$k], $options);
        }

        return $this;
    }

    public function flush()
    {
        foreach ($this->codes as $code)
        {
            if (empty($code->getOutputPath())) {
                echo $code->getCode();

                continue;
            }

            $this->writer->write($code->getOutputPath(), $code->getCode());
        }

        $this->clear();
    }

    public function clear()
    {
        $this->codeGeneratedStorage->clear();
        $this->codes = [];
    }
}
