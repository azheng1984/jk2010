#!/usr/bin/env php
<?php
namespace Hyperframework\Tool;

define('Hyperframework\Tool\ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
require ROOT_PATH . 'lib' . DIRECTORY_SEPARATOR . 'Hyperframework' .
    DIRECTORY_SEPARATOR . 'Tool' . DIRECTORY_SEPARATOR . 'Runner.php';
Runner::run();
