<?php

namespace Bonn\Generator\Storage;

use Bonn\Generator\Code;

final class CodeGeneratedStorage implements CodeGeneratedStorageInterface
{
    /**
     * @var array
     */
    private $codes = [];

    /**
     * @var array
     */
    private $outputs = [];

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->codes;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputsPath()
    {
        return $this->outputs;
    }

    /**
     * {@inheritdoc}
     */
    public function add($code, string $identifier, ?string $outputPath = null)
    {
        $this->codes[$identifier] = $code;
        $this->outputs[$identifier] = $outputPath;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $identifier)
    {
        unset($this->codes[$identifier]);
        unset($this->outputs[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->codes = [];
        $this->outputs = [];
    }
}
