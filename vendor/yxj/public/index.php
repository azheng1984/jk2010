<?php
namespace Yxj;

function run() {
    define('Yxj\ROOT_PATH', dirname(__DIR__));
    $configs = require ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
        . DIRECTORY_SEPARATOR . 'init.php';
    require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Web'
        . DIRECTORY_SEPARATOR . 'Runner.php';
    \Hyperframework\Web\Runner::run(__NAMESPACE__, ROOT_PATH, $configs);
};

run();
