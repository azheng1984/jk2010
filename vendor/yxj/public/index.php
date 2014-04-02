<?php
namespace Yxj;

define(__NAMESAPCE__ . '\ROOT_PATH', dirname(__DIR__));
define(__NAMESAPCE__ . '\INIT_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
    . DIRECTORY_SEPARATOR . 'init.php');
require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Web'
    . DIRECTORY_SEPARATOR . 'Runner.php';
\Hyperframework\Web\Runner::run(__NAMESPACE__, ROOT_PATH, require INIT_PATH);
