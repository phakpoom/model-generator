<?php

namespace Bonn\Generator\Storage;

interface CodeGeneratedStorageInterface
{
    /**
     * @return array
     */
    public function all();

    /**
     * @return array
     */
    public function getOutputsPath();

    /**
     * @param $data
     * @param string $identifier
     * @param string|null|null $outputPath
     */
    public function add($data, string $identifier, ?string $outputPath = null);

    /**
     * @param string $identifier
     */
    public function remove(string $identifier);

    public function clear();
}
