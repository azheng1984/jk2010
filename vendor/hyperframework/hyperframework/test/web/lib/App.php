<?php
namespace Hyperframework\Web\Test;

use Hyperframework\Web\App as Base;

class App extends Base {
    private static $callback = null;

    public static function setCreateAppCallback($callback) {
        self::$callback = $callback;
    }

    protected static function createApp() {
        $callback = self::$callback;
        $app = $callback();
        return $app;
    }
}
