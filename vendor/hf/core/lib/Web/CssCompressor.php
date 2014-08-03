<?php
namespace Hyperframework\Web;

class CssCompressor {
    public static function process($content) {
        //默认使用 nodejs => clean-css
        return $content;
    }
}
