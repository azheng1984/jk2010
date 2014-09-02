<?php
namespace Hyperframework;

class ClassFileHelper {
    public static function getClassNameByFileName($fileName) {
        $pattern = '/^([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*).php$/';
        if (preg_match($pattern, $fileName)) {
            return substr($fileName, 0, strlen($fileName) - 4);
        }
    }

    public static function getClasses($path) {
        $code = file_get_contents($path);
        $classes = array();
        $namespace = '';
        $tokens = token_get_all($code);
        $count = count($tokens);
        for ($index = 0; $index < $count; ++$index) {
            if (isset($tokens[$index][0]) === false) {
                continue;
            }
            if ($tokens[$index][0] === T_NAMESPACE) {
                $namespace = '';
                ++$index;
                while ($index < $count) {
                    if (isset($tokens[$index][0])
                        && $tokens[$index][0] === T_STRING
                    ) {
                        $namespace .= $tokens[$index][1] . '\\';
                    } elseif ($tokens[$index] === '{' || $tokens[$index] === ';'
                    ) {
                        break;
                    }
                    ++$index;
                }
            } elseif ($tokens[$index][0] === T_CLASS) {
                while ($index < $count) {
                    if (isset($tokens[$index][0])
                        && $tokens[$index][0] === T_STRING
                    ) {
                        $classes[] = $namespace . $tokens[$index][1];
                        break;
                    }
                    ++$index;
                }
            }
        }
        return $classes;
    }
}
