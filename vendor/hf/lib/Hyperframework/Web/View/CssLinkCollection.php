<?php
namespace Hyperframework\Web\View;

//manifest

CssLink::render('common');
//=> dev /asset/css/common.css or production http://cdn.com/site_name/css/common.css

//CssLink::render('http://cdn.com/ui.css');
//CssLink::render('common');
//CssLink::render('common');

class CssLinkCollection {
    private static $links = array();

    public static function add($href, $media = null) {
        static::$links[] = array($href, $media);
    }

    public static function render() {
        foreach (static::$links as $link) {
            CssLink::render($link[0], $link[1]);
        }
    }
}
