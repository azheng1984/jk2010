#!/usr/bin/env php
<?php
namespace Hyperframework\Tool;

function run() {
    $rootPath = __DIR__ . DIRECTORY_SEPARATOR;
    require $rootPath . 'lib' . DIRECTORY_SEPARATOR . 'Hyperframework' .
        DIRECTORY_SEPARATOR . 'Tool' . DIRECTORY_SEPARATOR . 'Bootstrap.php';
    Bootstrap::run($rootPath);
    \Hyperframework\Cli\Application\CliApplication::run();
}

run();
