<?php
namespace Hyperframework\Web;

class JsCompressor {
    public static function run($content) {
        return $content;
        //默认使用 nodejs => UglifyJs
    }
}
