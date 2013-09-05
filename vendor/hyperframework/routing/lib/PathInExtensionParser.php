<?php
namespace Hyperframework\Routing;

class PathInExtensionParser {
    public static function execute($originalPath = null) {
        if ($originalPath === null) {
            $requestUri = $_SERVER['REQUEST_URI'];
            $originalPath = explode('?', $requestUri, 2)[0];
        }
        $path = '/';
        $parameters = array();
        foreach (explode('/', $originalPath) as $segment) {
            if ($path !== '/') {
                $path .= '/';
            }
            if ($segment === '') {
                continue;
            }
            $position = strrpos($segment, '.');
            if ($position === false) {
                $path .= $segment;
                $parameters[] = null;
                continue;
            }
            $path .= substr($segment, $position + 1);
            $parameters[] = substr($segment, 0, $position);
        }
        return array('path' => $path, 'parameters' => $parameters);
    }
}
