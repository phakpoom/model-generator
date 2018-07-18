<?php

namespace Bonn\Generator;

interface CodeWriterInterface
{
    /**
     * @param string $path
     * @param $content
     */
    public function write(string $path, $content): void;
}
