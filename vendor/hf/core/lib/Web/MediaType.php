<?php
class MediaType {
    private static $mediaType;

    public static function initialize($pathInfo = null) {
        if (isset($_SERVER['REQUEST_MEDIA_TYPE'])) {
            self::$mediaType = $_SERVER['REQUEST_MEDIA_TYPE'];
            return;
        }
        if (isset($pathInfo['views']) === false) {
            self::$mediaType = null;
            return;
        }
        $views = $pathInfo['views'];
        if (is_string($views)) {
            self::$mediaType = $views;
            return;
        }
        self::$mediaType = $views[0];
    }

    public static function get() {
        return self::$mediaType;
    }
}
