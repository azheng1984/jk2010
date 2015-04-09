<?php
namespace Hyperframework\Common;

abstract class App {
    private $isQuitMethodCalled = false;

    /**
     * @param string $appRootPath
     */
    public function __construct($appRootPath) {
        Config::set('hyperframework.app_root_path', $appRootPath);
        if (Config::getBool('hyperframework.initialize_config', true)) {
            $this->initializeConfig();
        }
        if (Config::getBool(
            'hyperframework.initialize_error_handler', true
        )) {
            $this->initializeErrorHandler();
        }
    }

    public function quit() {
        if ($this->isQuitMethodCalled) {
            throw new InvalidOperationException(
                "The quit method of class '" . __CLASS__
                    . "' cannot be called more than once."
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
                $path = ConfigFileFullPathBuilder::build(
                    'environments' . DIRECTORY_SEPARATOR . $env . '.php'
                );
                if (file_exists($path)) {
                    Config::importFile($path);
                }
            }
        }
    }

    /**
     * @param string $defaultClass
     */
    protected function initializeErrorHandler($defaultClass = null) {
        $configName = 'hyperframework.error_handler.class';
        $class = Config::getString($configName, '');
        if ($class === '') {
            if ($defaultClass === null) {
                $handler = new ErrorHandler;
            } else {
                $handler = new $defaultClass;
            }
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Class '$class' does not exist, set "
                        . "using config '$configName'."
                );
            }
            $handler = new $class;
        }
        $handler->run();
    }

    protected function finalize() {}
}
