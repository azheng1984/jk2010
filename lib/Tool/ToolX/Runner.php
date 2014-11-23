<?php
namespace Hyperframework\Blog\Tool\ToolX;

use Hyperframework\Cli\Runner as Base;
use Hyperframework\Config;

class Runner extends Base {
    protected static function initialize() {
        parent::initialize();
        Config::import([
            '[hyperframework.cli]',
            'command_root_namespace' => __NAMESPACE__
            'command_config_root_path' => ROOT_PATH . '/config/tool/tool_x',
        ]);
    }
}
