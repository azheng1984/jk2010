<?php
namespace Hyperframework\Web\View;

class CssLink {
    public static function render($path, $isRelativePath = false) {
        echo '<script type="text/javascript" src="',
            Asset\JsUrl::get($path, $isRelativePath), '"></script>';
    }
}
