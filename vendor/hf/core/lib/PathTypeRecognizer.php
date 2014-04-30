<?php
namespace Hyperframework;

class PathTypeRecognizer {
    public static function isFull($path) {
        if (isset($path[0]) === false) {
            return false;
        }
        if ($path[0] === '/' || $path[0] === '\\') {
            return true;
        }
        if (DIRECTORY_SEPARATOR === '/' || isset($path[1]) === false) {
            return false;
        }
        if ($path[1] === ':') {
            return true;
        }
        return false;
    }
}
