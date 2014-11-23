<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\Runner as Base;

require HYPERFRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Web'
    . DIRECTORY_SEPARATOR . 'Runner.php';

class Runner extends Base {
    protected static function runApp() {
        $app = new App;
        $app->run();
    }
}
