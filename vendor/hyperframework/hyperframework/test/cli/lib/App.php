<?php
namespace Hyperframework\Cli\Test;

use Hyperframework\Cli\App as Base;

class App extends Base {
    protected static function createApp($appRootPath) {
        return $GLOBALS['app'];
    }
}
