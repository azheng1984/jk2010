<?php
namespace Hyperframework\Blog\Tool\ToolX;

use Hyperframework\Common\Config;
use Hyperframework\Cli\Runner as Base;

class Runner extends Base {
    protected static function initializeConfig() {
        parent::initializeConfig();
        Config::import('tool/tool_x/init.php');
    }
}
