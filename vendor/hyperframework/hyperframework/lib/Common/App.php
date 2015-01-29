<?php
namespace Hyperframework\Common;

abstract class App {
    public function __construct($appRootPath) {
        Config::set('hyperframework.app_root_path', $appRootPath);
        $this->initializeConfig();
        $this->initializeErrorHandler();
    }

    protected function initializeConfig() {
        Config::importFile('init.php');
    }

    protected function initializeErrorHandler() {
        $class = Config::getString('hyperframework.error_handler.class', '');
        if ($class === '') {
            $handler = static::getDefaultErrorHandler();
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Error handler class '$class' does not exist, "
                        . "defined in 'hyperframework.error_handler.class'."
                );
            }
            $handler = new $class;
        }
        $handler->run();
    }

    protected function getDefaultErrorHandler() {
        return new ErrorHandler;
    }
}
