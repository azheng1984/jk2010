<?php
namespace Hyperframework\Common;

use LogicException;

abstract class App {
    private $appRootPath;

    public function __construct($appRootPath = null) {
        $this->appRootPath = $appRootPath;
        if (Config::getBoolean('hyperframework.initialize_config', true)) {
            $this->initializeConfig();
        }
        if (Config::getBoolean(
            'hyperframework.initialize_error_handler', true
        )) {
            $this->initializeErrorHandler();
        }
    }

    protected function initializeConfig() {
        $this->initializeAppRootPath();
        Config::importFile('init.php');
    }

    protected function initializeAppRootPath() {
        if ($this->appRootPath === null) {
            throw new LogicException("App root path cannot be empty.");
        }
        Config::set('hyperframework.app_root_path', $appRootPath);
    }

    protected function initializeErrorHandler($defaultClass = null) {
        $class = Config::getString(
            'hyperframework.error_handler.class', ''
        );
        if ($class === '') {
            if ($defaultClass === null) {
                $handler = new ErrorHandler;
            } else {
                $handler = new $defaultClass;
            }
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
}
