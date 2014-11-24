<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\Runner as Base;

require ROOT_PATH . '/vendor/hyperframework/hyperframework/lib/Web/Runner.php';

class Runner extends Base {
    protected static function runApp() {
        $app = new App;
        $app->run();
    }
}
