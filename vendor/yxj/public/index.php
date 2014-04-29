<?php
namespace ProjectRootNamespace;

use Hyperframework\Web\Runner;

define('ProjectRootNamespace\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor'
    . DIRECTORY_SEPARATOR . 'hyperframework' . DIRECTORY_SEPARATOR . 'core'
    . DIRECTORY_SEPARATOR . 'Web' . DIRECTORY_SEPARATOR . 'Runner.php';
Runner::initialize(__NAMESPACE__, ROOT_PATH);
Runner::run();
