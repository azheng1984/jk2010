<?php
namespace Yxj;

define('Yxj\ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
require ROOT_PATH . 'lib' . DIRECTORY_SEPARATOR .
    'Yxj' . DIRECTORY_SEPARATOR . 'Runner.php';
Runner::run();
