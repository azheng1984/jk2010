<?php
namespace Hyperframework\Web;

class Router {
    public function execute($app) {
        $segments = RequestPath::getSegments();
        $ids = array();
        $path = '';
        foreach ($segments as $segment) {
            if (ctype_digit($segment)) {
                $path .= 'item';
                $ids[] = $segment;
            } elseif ($segment === 'item') {
                throw new NotFoundException;
            }
            $path .= '/' . $segment;
        }
        $idCount = count($ids);
        if ($idCount !== 0) {
            if ($idCount === 1) {
                $app->setParam('id', $ids[0]);
            } else {
                $index = 0;
                foreach ($ids as $id) {
                    $app->setParam('id_' . $index, $id);
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
}
