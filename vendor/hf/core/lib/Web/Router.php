<?php
namespace Hyperframework\Web;

final class Router {
    public static function run($ctx, $options = null) {
        $segments = RequestPath::getSegments();
        $params = array();
        $path = '';
        foreach ($segments as $segment) {
            if (static::isParam($segemnt)) {
                $path .= 'item';
                $params[] = $segment;
            } elseif ($segment === 'item') {
                throw new NotFoundException;
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
        return ctype_digit($segment[0]);
    }

    public function parse() {
        $config = array(
            '/{user_name}' => 'user',
            '/{user_name}/{project_name}' => 'project',
        );
        $config = array(
            '/articles' => 'articles',
            '/articles/{id}' => 'articles/item',
            '/articles/{id}/comments' => 'articles/item/comments',
            '/articles/{id}/comments/{id}' => 'articles/item/comments/item',
        );
        $config = array(
            '/articles' => 'articles',
            '/articles/{id}-{name}' => 'articles/item',
            '/articles/{id}/*' => 'articles/item',
        );
        $config = array(
            '/articles' => 'articles',
            '/articles/{id}-{name}' => 'articles/item',
        );
        '/{name}/{project_name}';
    }
}
