<?php
namespace Yxj;

define('Yxj\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor'
    . DIRECTORY_SEPARATOR . 'hyperframework_core' . DIRECTORY_SEPARATOR . 'Web'
    . DIRECTORY_SEPARATOR . 'Build' . DIRECTORY_SEPARATOR . 'Runner.php';
\Hyperframework\Web\Build\Runner::run(__NAMESPACE__, ROOT_PATH);
