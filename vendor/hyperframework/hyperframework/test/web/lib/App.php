<?php
namespace Hyperframework\Web\Test;

use Hyperframework\Web\App as Base;

class App extends Base {
    protected static function createApp() {
        return $GLOBALS['app'];
    }
}
