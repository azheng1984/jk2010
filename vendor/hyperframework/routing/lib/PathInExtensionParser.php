<?php
namespace Hyperframework\Routing;

class PathInExtensionParser {
    public static function checker($uri = null) {
        if ($uri === null) {
            $uri = $_SERVER['REQUEST_URI'];
        }
        $path = '/';
        $parameters = array();
        foreach (explode('/', explode('?', $uri, 2)[0]) as $segment) {
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
