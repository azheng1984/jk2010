<?php
namespace Hyperframework\Blog\Tool\ToolX;

use Hyperframework\Cli\Runner as Base;
use Hyperframework\Config;
use Hyperframework;

require Hyperframework\Blog\HYPERFRAMEWORK_PATH . '/Cli/Runner.php';

class Runner extends Base {
    protected static function initialize($appRootNamespace, $appRootPath) {
        parent::initialize($appRootNamespace, $appRootPath);
        Config::import([
            '[hyperframework.cli]',
            'command_root_namespace' => __NAMESPACE__,
            'command_config_root_path' => $appRootPath
                . DIRECTORY_SEPARATOR . 'config'
                . DIRECTORY_SEPARATOR . 'tool'
                . DIRECTORY_SEPARATOR . 'tool_x',
        ]);
    }
}
