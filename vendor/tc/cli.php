#!/usr/bin/env php
<?php
namespace Tc;

define('Tc\ROOT_PATH', __DIR__);
//require ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
//    . DIRECTORY_SEPARATOR . 'init_const.php';
//require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Cli'
//    . DIRECTORY_SEPARATOR . 'Runner.php';
require ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
    . DIRECTORY_SEPARATOR . 'init_const.php';
require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Cli'
    . DIRECTORY_SEPARATOR . 'Runner.php';
\Hyperframework\Cli\Runner::run(__NAMESPACE__, ROOT_PATH);

/*
function run() {
    $rootPath = __DIR__ . DIRECTORY_SEPARATOR;
    require $rootPath . 'lib' . DIRECTORY_SEPARATOR . 'Bootstrap.php';
    Bootstrap::run($rootPath);
}

run();
*/
