<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Config;

class Runner {
    public static function run() {
        static::initialize();
        static::runApp();
    }

    protected static function initialize() {
        static::initializeConfig();
        static::initializeErrorHandler();
    }

    protected static function initializeConfig() {
        Config::set('hyperframework.app_root_path', static::getAppRootPath());
        Config::import('init.php');
    }

    protected static function runApp() {
        throw new Exception;
    }

    protected static function initializeErrorHandler() {
        throw new Exception;
    }

    protected static function getAppRootPath() {
        throw new Exception;
    }
}
