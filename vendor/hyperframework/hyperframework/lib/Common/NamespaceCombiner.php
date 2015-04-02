<?php
namespace Hyperframework\Common;

class NamespaceCombiner {
    /**
     * @param string &$target
     * @param string $extra
     */
    public static function append(&$target, $extra) {
        $target = (string)$target;
        $extra = (string)$extra;
        if ($target !== '') {
            $target = rtrim($target, '\\');
            if ($target === '') {
                $target = '\\';
            }
        }
        if ($extra !== '') {
            $extra = trim($extra, '\\');
        }
        if ($extra === '') {
            return;
        }
        if ($target !== '\\') {
            $target .= '\\';
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
