<?php
namespace Yxj;

require ROOT_PATH . 'config' . DIRECTORY_SEPARATOR
    . 'env' . DIRECTORY_SEPARATOR . 'env.config.php';
require HYPERFRAMEWORK_PATH . '\Web\Runner.php';

class Runner extends \Hyperframework\Web\Runner {
    protected static function getHyperframeworkPath() {
        return HYPERFRAMEWORK_PATH;
    }

    protected static function getAppPath() {
        return ROOT_PATH;
    }
}
