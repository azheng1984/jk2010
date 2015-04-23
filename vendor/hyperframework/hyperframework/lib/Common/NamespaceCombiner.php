<?php
namespace Hyperframework\Common;

class NamespaceCombiner {
    /**
     * @param string $a
     * @param string $b
     */
    public static function combine($a, $b) {
        $target = (string)$a;
        $extra = (string)$b;
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
            return $a;
        }
        if ($target !== '\\') {
            $target .= '\\';
        }
        return $target . $extra;
    }
}
