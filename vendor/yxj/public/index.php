<?php
namespace Yxj;

define('Yxj\ROOT_PATH', dirname(__DIR__));
require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Web'
    . DIRECTORY_SEPARATOR . 'Runner.php';
\Hyperframework\Web\Runner::run(__NAMESPACE__, ROOT_PATH);
