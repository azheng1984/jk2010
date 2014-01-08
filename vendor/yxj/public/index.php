<?php
namespace Yxj;

define(__NAMESPACE__ . '\ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
require ROOT_PATH . 'config' . DIRECTORY_SEPARATOR
    . 'env' . DIRECTORY_SEPARATOR . 'env.config.php';
require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Hyperframework'
    . DIRECTORY_SEPARATOR . 'Web' . DIRECTORY_SEPARATOR . 'Runner.php';
\Hyperframework\Web\Runner::run(HYPERFRAMEWORK_PATH, __NAMESPACE__);
