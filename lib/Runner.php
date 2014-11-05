<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\Runner as Base;

class Runner extends Base {
    protected static function runApp() {
        $app = new App;
        $app->run();
    }
}
