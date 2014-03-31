<?php
namespace Hyperframework;

class PathTypeRecognizer {
    public static function isFull($path) {
        if (DIRECTORY_SEPARATOR === '/') {
            return strncmp($path, '/', 1) === 0;
        }
        return substr($path, 1, 1) === ':' || strncmp($path, '\\', 1) === 0;
    }
}
