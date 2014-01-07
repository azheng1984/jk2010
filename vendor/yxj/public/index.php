<?php
namespace Yxj;

define('Yxj\ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require ROOT_PATH . 'config' . DIRECTORY_SEPARATOR
    . 'env' . DIRECTORY_SEPARATOR . 'env.config.php';

require HYPERFRAMEWORK_PATH . '\Web\Runner.php';

Hyperframework\Web\Runner::run(HYPERFRAMEWORK_PATH, ROOT_PATH);
