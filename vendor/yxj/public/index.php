<?php
namespace ProjectNamespace;

use Hyperframework\Web\EnvironmentBuilder;
use Hyperframework\Web\Runner;

define('ProjectNamespace\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor'
    . DIRECTORY_SEPARATOR . 'hyperframework' . DIRECTORY_SEPARATOR . 'core'
    . DIRECTORY_SEPARATOR . 'Web' . DIRECTORY_SEPARATOR . 'Initializer.php';
EnvironmentBuilder::run(__NAMESPACE__, ROOT_PATH);
Runner::run();
