<?php
namespace Hyperframework\Blog\Tool\ToolX;

use Hyperframework\Cli\Runner as Base;
use Hyperframework\Config;
use Hyperframework;

if (class_exists('Hyperframework\Cli\Runner') === false) {
    require Hyperframework\Blog\HYPERFRAMEWORK_PATH . '/Cli/Runner.php';
}

class Runner extends Base {
    protected static function initialize($appRootNamespace, $appRootPath) {
        parent::initialize($appRootNamespace, $appRootPath);
        //Config::import('task_x/init.php');
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
