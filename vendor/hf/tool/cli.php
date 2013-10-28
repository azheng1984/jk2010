#!/usr/bin/env php
<?php
namespace Hyperframework\Tool;

function run() {
    $rootPath = __DIR__ . DIRECTORY_SEPARATOR;
    require $rootPath . 'lib' . DIRECTORY_SEPARATOR . 'Bootstrap.php';
    Bootstrap::run($rootPath);
    $app = new CommandApplication;
    $app->run();
}

run();
