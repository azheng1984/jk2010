<?php
define('HYPERFRAMEWORK_PATH', '/usr/lib/hf/');
require HYPERFRAMEWORK_PATH . 'Config.php';
Hyperframework\Config::set(
    ['Hyperframework\Web\PathInfo\EnableCache', false],
    ['Hyperframework\Web\View\Asset\EnableCache', false],
    ['Hyperframework\ClassLoader\EnableCache', false],
);

/*
define('ENABLE_HYPERFRAMEWORK_PATH_INFO_CACHE', false);
define('ENABLE_HYPERFRAMEWORK_CLASS_LOADER_CACHE', false);
define('ENABLE_HYPERFRAMEWORK_ASSET_CACHE', false);
*/
