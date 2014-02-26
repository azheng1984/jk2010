<?php
namespace Yxj;

require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Hyperframework'
    . DIRECTORY_SEPARATOR . 'Web' . DIRECTORY_SEPARATOR . 'Runner.php';

class Runner extends \Hyperframework\Web\Runner {
    public static function run() {
        parent::run(HYPERFRAMEWORK_PATH, __NAMESPACE__);
    }

    protected static function getPath() {
        $requestPath = parent::getPath();
        //use custom router
    }

    protected static function runApplication($path) {
        //use new app type
    }

    protected static function isAsset() {
        //disable asset proxy
        return false;
    }
}
