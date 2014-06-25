<?php
namespace ProjectNamespace;

define('ProjectNamespace\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor'
    . DIRECTORY_SEPARATOR . 'hyperframework' . DIRECTORY_SEPARATOR . 'core'
    . DIRECTORY_SEPARATOR . 'Web' . DIRECTORY_SEPARATOR . 'Initializer.php';
\Hyperframework\Web\EnvironmentBuilder::run(__NAMESPACE__, ROOT_PATH);
\Hyperframework\Web\Runner::run();
