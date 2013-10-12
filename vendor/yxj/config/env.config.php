<?php
namespace Yxj;

const HYPERFRAMEWORK_PATH = '/usr/local/hyperframework/lib/Hyperframework/';
require HYPERFRAMEWORK_PATH . 'Config.php';
Hyperframework\Config::set(array(
    'Hyperframework\Web\PathInfo\EnableCache' => false,
    'Hyperframework\Web\View\Asset\EnableCache' => false,
    'Hyperframework\ClassLoader\EnableCache' => false
));
