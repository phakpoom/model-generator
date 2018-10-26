<?php

namespace Bonn\Generator\Model;

class NameResolver
{
    /**
     * @param string $string
     * @return string|null
     */
    public static function resolveOnlyInterfaceName(string $string): ?string
    {
        if (false === strpos($string, 'Interface')) {
            return null;
        }

        $arr = explode('\\', $string);

        return end($arr);
    }

    /**
     * @param string $string
     * @return string|null
     */
    public static function resolveOnlyClassName(string $string): ?string
    {
        $arr = explode('\\', $string);
        return end($arr);
    }
}
