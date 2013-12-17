<?php
namespace Hyperframework\Web\View;

class CssLink {
    public static function render(
        $path, $media = null, $isRelativePath = false
    ) {
        echo '<link rel="stylesheet" type="text/css" href="',
            Asset\CssUrl::get($path, $isRelativePath), '"';
        if ($media !== null) {
            echo ' media="', $media, '"';
        }
        echo '/>';
    }
}
