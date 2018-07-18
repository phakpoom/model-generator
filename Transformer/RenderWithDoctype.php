<?php

namespace Bonn\Generator\Transformer;

use Nette\PhpGenerator\Helpers;

final class RenderWithDoctype
{
    public static function render($content)
    {
        return str_replace("\n\n\n", "\n\n", Helpers::tabsToSpaces("<?php\n\ndeclare(strict_types=1);\n\n" . $content));
    }
}
