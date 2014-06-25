<?php
namespace Hft;

use Hyperframework\Web\EnvironmentBuilder;
use Hyperframework\Web\Runner;

define('Hft\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/config/init_const.php';
require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'EnvironmentBuilder.php';
EnvironmentBuilder::run(__NAMESPACE__, ROOT_PATH);
echo 'hi';
exit;
Runner::run();
