<?php
namespace Hyperframework\Blog\Tool\ToolX;

use Hyperframework\Cli\Runner as Base;
use Hyperframework\Common\Config;
use Hyperframework;

if (class_exists('Hyperframework\Cli\Runner') === false) {
    require Hyperframework\Blog\HYPERFRAMEWORK_PATH . '/Cli/Runner.php';
}

class Runner extends Base {
    protected static function initialize(
        $appRootNamespace = null, $appRootPath = null
    ) {
        parent::initialize($appRootNamespace, $appRootPath);
        Config::import('tool/tool_x/init.php');
    }
}
