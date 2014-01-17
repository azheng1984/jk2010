<?php
namespace Yxj;

define(__NAMESPACE__ . '\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR
    . 'env' . DIRECTORY_SEPARATOR . 'init.php';
require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Hyperframework'
    . DIRECTORY_SEPARATOR . 'Web' . DIRECTORY_SEPARATOR . 'Runner.php';
\Hyperframework\Web\Runner::run(HYPERFRAMEWORK_PATH, __NAMESPACE__);
