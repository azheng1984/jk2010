#!/usr/bin/env php
<?php
namespace Hyperframework\Blog;

//include dirname(__DIR__) . '/include/tool_consts.php';
define(__NAMESPACE__ . '\ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . DIRECTORY_SEPARATOR . 'config'
    . DIRECTORY_SEPARATOR . 'init_const.php';

require ROOT_PATH . DIRECTORY_SEPARATOR . 'lib'
    . DIRECTORY_SEPARATOR . 'Tool/TootX/Runner.php';
Runner::run(__NAMESPACE__, ROOT_PATH);
