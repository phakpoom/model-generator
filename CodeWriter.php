<?php

namespace Bonn\Generator;

use Symfony\Component\Filesystem\Filesystem;

class CodeWriter implements CodeWriterInterface
{
    /**
     * @param string $path
     * @param $content
     */
    public function write(string $path, $content): void
    {
        $fs = new Filesystem();
        $explodedPath = explode('/', $path);
        array_pop($explodedPath);
        $fs->mkdir(implode('/', $explodedPath));
        file_put_contents($path, $content);
        dump($path . ' was created');
    }
}
