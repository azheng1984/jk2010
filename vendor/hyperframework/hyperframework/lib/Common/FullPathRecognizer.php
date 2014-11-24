<?php
namespace Hyperframework\Common;

class FullPathRecognizer {
    public static function isFull($path) {
        if (isset($path[0]) === false) {
            return false;
        }
        if (DIRECTORY_SEPARATOR === '/') {
            return $path[0] === '/';
        }
        if ($path[0] === '/' || $path[0] === '\\') {
            return true;
        }
        if (isset($path[1]) === false) {
            return false;
        }
        if ($path[1] === ':') {
            return true;
        }
        return false;
    }
}
