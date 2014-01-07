<?php
namespace Yxj;

require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Hyperframework'
    . DIRECTORY_SEPARATOR . 'Web' . DIRECTORY_SEPARATOR . 'Runner.php';

class Runner extends \Hyperframework\Web\Runner {
    protected static function getPath() {
        $requestPath = parent::getPath();
        //use custom router
    }

    protected static function runApp($path) {
        //use new app type
    }

    protected static function isAsset() {
        //disable asset proxy
        return false;
    }
}
