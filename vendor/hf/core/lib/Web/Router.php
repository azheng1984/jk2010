<?php
namespace Hyperframework\Web;

class Router {
    public static function execute($ctx) {
        $segments = RequestPath::getSegments();
        $ids = array();
        $path = '';
        foreach ($segments as $segment) {
            if (static::isId($path, $segemnt)) {
                $path .= 'item';
                $ids[] = $segment;
            } elseif ($segment === 'item') {
                throw new NotFoundException;
            }
            $path .= '/' . $segment;
        }
        if ($path === '') {
            return '/';
        }
        $idCount = count($ids);
        if ($idCount !== 0) {
            if ($idCount === 1) {
                $ctx->setParam('id', $ids[0]);
            } else {
                $index = 0;
                foreach ($ids as $id) {
                    $ctx->setParam('id_' . $index, $id);
                    ++$index;
                }
            }
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

    protected static function isId($segment) {
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
