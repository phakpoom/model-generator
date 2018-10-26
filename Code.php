<?php

namespace Bonn\Generator;

class Code
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string|null
     */
    private $outputPath;

    public function __construct(string $code, ?string $outputPath = null)
    {
        $this->code = $code;
        $this->outputPath = $outputPath;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getOutputPath(): ?string
    {
        return $this->outputPath;
    }

    /**
     * @param string|null $outputPath
     */
    public function setOutputPath(?string $outputPath)
    {
        $this->outputPath = $outputPath;
    }
}
