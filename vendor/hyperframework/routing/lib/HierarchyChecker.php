<?php
namespace Hyperframework\Routing;

class HierarchyChecker {
    const FILE = 0;
    const DIRECTORY = 1;

    public static function check($path = null) {
        if ($path === null) {
            $path = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        }
        if (\Hyperframework\Web\PathInfo::exists($path)) {
           return;
        }
        if (substr($path, -1) === '/') {
            return static::redirectToFile($path);
        }
        return static::redirectToDirectory($path);
   }

    private static function redirectToFile($path) {
        $path = substr($path, 0, strlen($path) - 1);
        if (\Hyperframework\Web\PathInfo::exists($path)) {
            return self::FILE;
        }
        throw new \Hyperframework\Web\NotFoundException;
    }

    private static function redirectToFile($path) {
        $path = $path . '/';
        if (\Hyperframework\Web\PathInfo::exists($path)) {
            return self::DIRECTORY;
        }
        throw new \Hyperframework\Web\NotFoundException;
    }
}
