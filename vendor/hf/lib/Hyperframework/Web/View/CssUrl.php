<?php
namespace Hyperframework\Web\View;

class CssUrl extends AssetUrl {
    protected static function getDefaultRootPath() {
        return '/asset/css';
    }
}
