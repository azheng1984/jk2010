<?php
namespace Hyperframework\Blog\Tool\ToolX;

use Hyperframework;

return [
    '[hyperframework.cli]',
    'command_root_namespace' => __NAMESPACE__,
    'command_config_root_path' => Hyperframework\APP_ROOT_PATH
        . DIRECTORY_SEPARATOR . 'config'
        . DIRECTORY_SEPARATOR . 'tool'
        . DIRECTORY_SEPARATOR . 'tool_x',
];
