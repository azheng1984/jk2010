<?php
namespace Hyperframework\Routing;

class DashParameterFilter {
    public static function execute($originalPath = null) {
        if ($originalPath === null) {
            $requestUri = $_SERVER['REQUEST_URI'];
            $originalPath = explode('?', $requestUri, 2)[0];
        }
        $path = '';
        $parameters = array();
        foreach (explode('/', $originalPath) as $segment) {
            if ($segment === '') {
                if ($path !== '') {
                    $path .= '/';
                }
                continue;
            }
            $splittingPosition = strpos($segment, '-');
            if ($splittingPosition === false) {
                $path .= '/' . $segment;
                $parameters[] = null;
                continue;
            }
            $items = explode('-', $segment, 2);
            $path .= '/' . $items[0];
            $parameters[] = $items[1];
        }
        return array('path' => $result, 'parameters' => $parameters);
    }
}
