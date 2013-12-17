<?php
namespace Hyperframework\Web\View;

class CssLink {
    private static $links = array();

    public static function add($href, $media = null) {
        static::$links[] = array($href, $media);
    }

    public static function render() {
        foreach (static::$links as $link) {
            echo '<link rel="stylesheet" type="text/css" href="', $link[0], '"';
            if ($link[1] !== null) {
                echo ' media="screen"';
            }
            echo '/>';
        }
        static::$links[] = array();
    }
}
