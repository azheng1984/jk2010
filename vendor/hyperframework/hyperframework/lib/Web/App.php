<?php
namespace Hyperframework\Web;

use LogicException;
use Hyperframework\Common\Config;
use Hyperframework\Common\NamespaceCombiner;
use Hyperframework\Common\ClassNotFoundException;
use Hyperframework\Common\App as Base;

class App extends Base {
    private $router;

    public function __construct() {
        parent::__construct();
        $this->rewriteRequestMethod();
        $this->checkCsrf();
    }

    public static function run() {
        $app = new static;
        $controller = $app->createController();
        $controller->run();
        $app->finalize();
    }

    public function getRouter() {
        if ($this->router === null) {
            $class = Config::getString('hyperframework.web.router_class', '');
            if ($class === '') {
                $class = 'Router';
                $namespace = Config::getAppRootNamespace();
                if ($namespace !== '' && $namespace !== '\\') {
                    NamespaceCombiner::prepend($class, $namespace);
                }
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Router class '$class' does not exist."
                    );
                }
            } elseif (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Router class '$class' does not exist, defined in "
                        . "'hyperframework.web.router_class'."
                );
            }
            $this->router = new $class($this);
        }
        return $this->router;
    }

    public function quit() {
        $this->finalize();
        $this->exitScript();
    }

    protected function rewriteRequestMethod() {
        if (Config::getBoolean(
            'hyperframework.web.rewrite_request_method', true
        )) {
            $method = null;
            if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
            } elseif (isset($_POST['_method'])) {
                $method = $_POST['_method'];
            }
            if ($method !== null && $method !== '') {
                $_SERVER['ORIGINAL_REQUEST_METHOD'] =
                    $_SERVER['REQUEST_METHOD'];
                $_SERVER['REQUEST_METHOD'] = strtoupper($method);
            }
        }
    }

    protected function checkCsrf() {
        CsrfProtection::run();
    }

    protected function createController() {
        $router = $this->getRouter();
        $class = (string)$router->getControllerClass();
        if ($class === '') {
            throw new LogicException('Controller class cannot be empty.');
        }
        if (class_exists($class) === false) {
            throw new ClassNotFoundException(
                "Controller class '$class' does not exist."
            );
        }
        return new $class($this);
    }

    protected function initializeAppRootPath() {
        Config::set('hyperframework.app_root_path', dirname(getcwd()));
    }

    protected function initializeErrorHandler($defaultClass = null) {
        if ($defaultClass === null) {
            $defaultClass = 'Hyperframework\Web\ErrorHandler';
        }
        parent::initializeErrorHandler($defaultClass);
    }

    protected function finalize() {}

    private function exitScript() {
        exit();
    }
}
