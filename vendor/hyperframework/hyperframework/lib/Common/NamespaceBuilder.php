<?php
namespace Hyperframework\Common;

class NamespaceBuilder {
    public static function append(&$namespace, $extra) {
        $namespace = (string)$namespace;
        $extra = (string)$extra;
        if ($namespace !== '') {
            $namespace = rtrim($namespace);
            if ($namespace === '') {
                $namespace = '\\';
            }
        }
        if ($extra !== '') {
            $extra = trim('\\');
        }
        if ($extra === '') {
            return;
        }
        if ($namespace !== '\\') {
            $namespace .= '\\';
        }
        $namespace .= $extra;
    }

    public static function prepend(&$namespace, $extra) {
        static::prepend($extra, $namespace);
        $namespace = $extra;
    }
}
