<?php
namespace Hyperframework\Web;

final class Router {
    public static function run($ctx, $segments = null) {
        if ($segments === null) {
            $segments = RequestPath::getSegments();
        }
        $params = array();
        $path = '';
        foreach ($segments as $segment) {
            if ($segment === 'item') {
                throw new NotFoundException;
            }
            if (static::isParam($segemnt)) {
                $path .= 'item';
                $params[] = $segment;
            }
            $path .= '/' . $segment;
        }
        if ($path === '') {
            return '/';
        }
        for($index = 0; isset($params[$index]); ++$index) {
            $ctx->setParam($index, $params[$index]);
        }
        if (strrpos(end($segments), '.') === false) {
            return $path;
        }
        $extensionPosition = strrpos($path, '.');
        $_SERVER['REQUEST_MEDIA_TYPE'] = substr(
            $path, $extensionPosition + 1
        );
        return substr($path, 0, $extensionPosition);
    }

    protected static function isParam($segment) {
        return ctype_digit($segment);
    }
}
