<?php
namespace Hyperframework\Common;

class FilePathCombiner {
    /**
     * @param string &$target
     * @param string $extra
     */
    public static function append(&$target, $extra) {
        $target = (string)$target;
        $extra = (string)$extra;
        $separator = '/' === DIRECTORY_SEPARATOR ? '/' : '\/';
        if ($target !== '') {
            $target = rtrim($target, $separator);
            if ($target === '') {
                $target = DIRECTORY_SEPARATOR;
            }
        }
        if ($extra !== '') {
            $extra = trim($extra, $separator);
        }
        if ($extra === '') {
            return;
        }
        if ($target !== DIRECTORY_SEPARATOR) {
            $target .= DIRECTORY_SEPARATOR;
        }
        $target .= $extra;
    }

    /**
     * @param string &$target
     * @param string $extra
     */
    public static function prepend(&$target, $extra) {
        static::append($extra, $target);
        $target = $extra;
    }
}
