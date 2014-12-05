<?php
namespace Hyperframework\Common;

class Runner {
    protected static function initialize() {
        static::initializeAppRootPath();
        static::initializeConfig();
        static::initializeErrorHandler();
    }

    protected static function initializeAppRootPath() {
        throw new Exception;
    }

    protected static function initializeConfig() {
        Config::import('init.php');
    }

    protected static function initializeErrorHandler() {
        ErrorHandler::run();
    }
}
