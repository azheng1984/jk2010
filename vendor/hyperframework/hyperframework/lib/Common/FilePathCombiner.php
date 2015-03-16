<?php
namespace Hyperframework\Common;

class FilePathCombiner {
    public static function append(&$path, $extra) {
        $path = (string)$path;
        $extra = (string)$extra;
        $separator = '/' === DIRECTORY_SEPARATOR ? '/' : '\/';
        if ($path !== '') {
            $path = rtrim($path, $separator);
            if ($path === '') {
                $path = DIRECTORY_SEPARATOR;
            }
        }
        if ($extra !== '') {
            $extra = trim($extra, $separator);
        }
        if ($extra === '') {
            return;
        }
        if ($path !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }
        $path .= $extra;
    }

    public static function prepend(&$path, $extra) {
        static::append($extra, $path);
        $path = $extra;
    }
}
