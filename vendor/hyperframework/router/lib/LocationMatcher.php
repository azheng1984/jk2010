<?php
namespace Hyperframework\Router;

class LocationMatcher {
    public static function execute($location) {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return;
        }
        $requestLocation = (isset($_SERVER['HTTPS']) ? 'https' : 'http') .
            '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if ($location !== $requestLocation) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $location);
            return false;
        }
    }
}
