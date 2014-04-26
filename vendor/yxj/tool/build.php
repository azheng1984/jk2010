<?php
namespace Yxj;

define('Yxj\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor'
    . DIRECTORY_SEPARATOR . 'hyperframework.core' . DIRECTORY_SEPARATOR
    . 'Build' . DIRECTORY_SEPARATOR . 'Runner.php';
\Hyperframework\Build\Runner::run(__NAMESPACE__, ROOT_PATH);
