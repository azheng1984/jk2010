<?php
namespace Hyperframework\Common;

class Runner {
    protected static function initialize() {
        static::initializeAppRootPath();
        static::initializeConfig();
        static::initializeErrorHandler();
    }

    protected static function initializeAppRootPath() {
        throw new NotImplementedException(
            __METHOD__ . ' is not implemented.'
        );
    }

    protected static function initializeConfig() {
        Config::import('init.php');
    }

    protected static function initializeErrorHandler() {
        $class = Config::getString('hyperframework.error_handler.class', '');
        if ($class === '') {
            $class = static::getDefaultErrorHandlerClass();
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Error handler class '$class' does not exist."
                );
            }
        }
        $handler = new $class;
        $handler->run();
    }

    protected static function getDefaultErrorHandlerClass() {
        return 'Hyperframework\Common\ErrorHandler';
    }
}
