<?php
namespace Yxj;

const HYPERFRAMEWORK_PATH = '/usr/local/hyperframework/lib/Hyperframework/';
require HYPERFRAMEWORK_PATH . 'Config.php';
Hyperframework\Config::set(array(
    'Hyperframework\Web\PathInfo\CacheEnabled' => false,
    'Hyperframework\Web\View\Asset\CacheEnabled' => false,
    'Hyperframework\ClassLoader\CacheEnabled' => false
));
