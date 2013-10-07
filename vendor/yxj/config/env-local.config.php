<?php
require HF_PATH . 'Config.php';
use Hyperframework\Config;
Config::set(
    ['Hyperfraemwork\Web\PathInfo', 'enable_cache', false]
);
define('ENABLE_HYPERFRAMEWORK_PATH_INFO_CACHE', false);
define('ENABLE_HYPERFRAMEWORK_CLASS_LOADER_CACHE', false);
define('ENABLE_HYPERFRAMEWORK_ASSET_CACHE', false);
