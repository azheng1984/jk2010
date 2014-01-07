<?php
namespace Yxj;

require HYPERFRAMEWORK_PATH . '\Web\Runner.php';

class Runner extends \Hyperframework\Web\Runner {
    protected function getPath() {
        $requestPath = parent::getPath();
        //router...
    }

    protected function name($param) {
        return null;
    }
}
