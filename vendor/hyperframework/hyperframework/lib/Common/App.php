<?php
namespace Hyperframework\Common;

use LogicException;

abstract class App {
    private $isQuitMethodCalled = false;

    public function __construct($appRootPath) {
        Config::set('hyperframework.app_root_path', $appRootPath);
        if (Config::getBoolean('hyperframework.initialize_config', true)) {
            $this->initializeConfig();
        }
        if (Config::getBoolean(
            'hyperframework.initialize_error_handler', true
        )) {
            $this->initializeErrorHandler();
        }
    }

    public function quit() {
        if ($this->isQuitMethodCalled) {
            $class = get_called_class();
            throw new LogicException(
                "The quit method of $class cannot be called more than once."
            );
        }
        $this->isQuitMethodCalled = true;
        $this->finalize();
        ExitHelper::exitScript();
    }

    protected function initializeConfig() {
        Config::importFile('init.php');
        if (isset($_ENV['HYPERFRAMEWORK_ENV'])) {
            $env = (string)$_ENV['HYPERFRAMEWORK_ENV'];
            if ($env !== '') {
                $path = ConfigFileLoader::getFullPath(
                    'env' . DIRECTORY_SEPARATOR . $env . '.php'
                );
                if (file_exists($path)) {
                    Config::importFile($path);
                }
            }
        }
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
                    "Error handler class '$class' does not exist, set "
                        . "using config 'hyperframework.error_handler.class'."
                );
            }
            $handler = new $class;
        }
        $handler->run();
    }

    protected function finalize() {}
}
