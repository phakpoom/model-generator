<?php

namespace Bonn\Generator;

class DummyWriter implements CodeWriterInterface
{
    /**
     * @param string $path
     * @param $content
     */
    public function write(string $path, $content): void
    {
        dump($path . ' was created');

        echo $content;
    }
}
