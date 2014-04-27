<?php
namespace Yxj;

define('Yxj\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor'
    . DIRECTORY_SEPARATOR . 'hyperframework' . DIRECTORY_SEPARATOR . 'core'
    . DIRECTORY_SEPARATOR . 'Web' . DIRECTORY_SEPARATOR . 'Runner.php';
require ROOT_PATH . DIRECTORY_SEPARATOR . 'lib'
    . DIRECTORY_SEPARATOR . 'Runner.php';
\Hyperframework\Web\Runner::run(__NAMESPACE__, ROOT_PATH);
