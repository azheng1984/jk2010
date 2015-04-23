<?php
namespace Hyperframework\Common;

class FilePathCombiner {
    /**
     * @param string $a
     * @param string $b
     */
    public static function combine($a, $b) {
        $target = (string)$a;
        $extra = (string)$b;
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
            return $target;
        }
        if ($target !== DIRECTORY_SEPARATOR) {
            $target .= DIRECTORY_SEPARATOR;
        }
        return $target . $extra;
    }
}
