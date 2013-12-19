<?php
namespace Hyperframework\Web\View;

class JsUrl extends AssetUrl {
    protected static function getDefaultRootPath() {
        return '/asset/js';
    }
}
