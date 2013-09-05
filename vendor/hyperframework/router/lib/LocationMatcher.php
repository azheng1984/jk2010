<?php
namespace Hyperframework\Router;

class LinkMatcher {
    public static function execute($info) {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' ||
            $_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            return;
        }
        $location = static::getCurrent($info);
        if ($location === null) {
            return;
        }
        if ($location !== static::getRequestLink()) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $location);
            return false;
        }
    }

    protected static function getCurrentLink($info) {
        if (isset($info['Link']['can_get_current'])) {
            return $info['Link']['class']::getCurrent();
        }
    }

    protected static function getRequestLink() {
        return (isset($_SERVER['HTTPS']) ? 'https' : 'http') .
            '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}
