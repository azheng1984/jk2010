<?php
namespace Hyperframework\Web\View;

class ImageUrl extends AssetUrl {
    protected static function getDefaultRootPath() {
        return '/asset/image';
    }
}
