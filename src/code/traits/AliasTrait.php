<?php

namespace code\traits;

trait AliasTrait {

    public static function getAlias($alias, $throwException = true) {
        if (strncmp((string) $alias, '@', 1) !== 0) {
            // not an alias
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (defined($root)) {
            if (is_string(constant($root))) {
                return $pos === false ? constant($root) : constant($root) . substr($alias, $pos);
            }
        }

        if ($throwException) {
            throw new InvalidArgumentException("Invalid path alias: $alias");
        }

        return false;
    }

    public static function setAlias($alias, $path) {
        if (strncmp($alias, '@', 1)) {
            $alias = '~' . $alias;
        }
        if (!defined($alias)) {
            define($alias, $path);
        }
    }

    /**
     * 
     * @param string $alias
     * @return boolean
     */
    public function isAlias(string $alias) {
        if (strncmp((string) $alias, '@', 1) !== 0) {
            return false;
        }
        return true;
    }

}
