<?php
namespace ProjectName;

use Hyperframework\Web\Initializer;
use Hyperframework\Web\Runner;

define('ProjectName\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor'
    . DIRECTORY_SEPARATOR . 'hyperframework' . DIRECTORY_SEPARATOR . 'core'
    . DIRECTORY_SEPARATOR . 'Web' . DIRECTORY_SEPARATOR . 'Initializer.php';
Initializer::run(__NAMESPACE__, ROOT_PATH);
Runner::run();
