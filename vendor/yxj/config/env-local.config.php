<?php
require HYPERFRAMEWORK_PATH . 'Config.php';
Hyperframework\Config::set(
    ['Hyperframework\Web\PathInfo', 'enable_cache', false],
    ['Hyperframework\ClassLoader', 'enable_cache', false]
    ['Hyperframework\View\Asset', 'enable_cache', false]
);
/*
define('ENABLE_HYPERFRAMEWORK_PATH_INFO_CACHE', false);
define('ENABLE_HYPERFRAMEWORK_CLASS_LOADER_CACHE', false);
define('ENABLE_HYPERFRAMEWORK_ASSET_CACHE', false);
*/
