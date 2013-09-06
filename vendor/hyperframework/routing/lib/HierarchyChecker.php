<?php
namespace Hyperframework\Routing;
use Hyperframework\Web\PathInfo as PathInfo;
use Hyperframework\Web\NotFoundException as NotFoundException;

class HierarchyChecker {
    const FILE = 0;
    const DIRECTORY = 1;

    public static function check($path = null) {
        if ($path === null) {
            $path = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        }
        if (PathInfo::exists($path)) {
           return;
        }
        if (substr($path, -1) === '/') {
            $path = substr($path, 0, strlen($path) - 1);
            if (PathInfo::exists($path)) {
                return self::FILE;
            } else {
                throw new NotFoundException;
            }
        }
        $path = $path . '/';
        if (PathInfo::exists($path)) {
            return self::DIRECTORY;
        }
        throw new NotFoundException;
    }
}
