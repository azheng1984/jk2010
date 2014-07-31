<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class AssetUrl {
    public static function get($path) {
        $prefix = Config::get('hyperframework.asset_url_prefix');
        if ($prefix !== null) {
            return $prefix . $path;
        }
        return $path;
    }
}
