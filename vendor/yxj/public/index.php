<?php
namespace Yxj;

function run() {
    $rootPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    require $rootPath . 'lib' . DIRECTORY_SEPARATOR . 'Bootstrap.php';
    Bootstrap::run($rootPath);
}

run();
