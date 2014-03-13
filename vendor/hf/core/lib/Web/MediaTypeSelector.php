<?php
class MediaTypeSelector {
    public static function select($pathInfo) {
        if (isset($_SERVER['REQUEST_MEDIA_TYPE'])) {
            return $_SERVER['REQUEST_MEDIA_TYPE'];
        }
        if (isset($pathInfo['views']) === false) {
            return;
        }
        $views = $pathInfo['views'];
        if (is_string($views)) {
            return $views;
        }
        return $views[0];
    }
}
