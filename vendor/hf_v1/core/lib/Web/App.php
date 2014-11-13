<?php
namespace Hyperframework\Web;

use Exception;

class App {
    private $router;
    private $actionResult;
    private $view;
    private $isViewEnabled = true;

    public function run() {
        $this->initialize();
        $this->executeAction();
        $this->finalize();
    }

    public function getRouter() {
        return $this->router;
    }

    protected function setRouter($router) {
        $this->router = $router;
    }

    public function getActionResult($name = null) {
        if ($name === null) {
            $result = $this->actionResult;
        }
        return $this->actionResult[$name];
    }

    public function setActionResult($value) {
        return $this->actionResult = $value;
    }

    public function redirect($url, $statusCode = 302) {
        header('Location: ' . $url, true, $statusCode);
        $this->quit();
    }

    final public function disableView() {
        $this->isViewEnabled = false;
    }

    final public function enableView() {
        $this->isViewEnabled = true;
    }

    public function setView($value) {
        $this->view = $value;
    }

    public function getView() {
        return $this->view;
    }

    public function quit() {
        $this->finalize();
        exit;
    }

    protected function initialize() {
        $this->rewriteRequestMethod();
        $this->parseRequestBody();
        $this->initializeRouter();
    }

    protected function executeAction() {
        $router = $this->getRouter();
        $controllerClass = $router->getControllerClass();
        if ($controllerClass === null
            || class_exists($controllerClass) === false
        ) {
            throw new NotFoundException;
        }
        $controller = new $controllerClass($this);
        $controller->run();
        $filters = $controller->getFilters();
        foreach ($filters as $filter) {
            switch ($filter[0]) {
                case 'before':
                    break;
                case 'after':
                    break;
                case 'after_throwing':
                    break;
                case 'after_returning':
                    break;
                case 'around':
                    break;
            }
        }
        $actionMethod = $router->getActionMethod();
        if ($actionMethod === null) {
            throw new NotFoundException;
        }
        try {
            if (method_exists($controller, $actionMethod)) {
                $this->actionResult = $controller->$actionMethod();
            }
            if ($controller->isRendered()) {
                $controller->render();
            }
        } catch (Exception $e) {
            //execute after throwing filter
        }
        //execute after returning filter
    }

    protected function render() {
        $this->setRenderStatus(true);
        if ($this->isViewRendered === false) {
            return;
        }
        $view = $this->getView();
        if (is_object($view)) {
            if (method_exsits($view, 'render')) {
                $view->render();
                return;
            } else {
                throw new Exception;
            }
        }
        if ($view === null) {
            $router = $this->getRouter();
            if ($router->getModule() !== null) {
                $view .= $module;
            }
            $view .= $router->getController() . '/' . $router->getAction();
            if ($router->hasParam('format')) {
                $view .= '.' . $router->getParam('format');
            }
            $view .= '.php';
        } elseif (is_string($view) === false) {
            throw new Exception;
        }
        $template = new ViewTemplate($app->getActionResult());
        $template->render($view);
    }

    protected function finalize() {}

    //todo move to router
    protected function rewriteRequestMethod() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['_method']) &&
            Config::get('hyperframework.rewrite_request_method') !== false
        ) {
            $_SERVER['ORIGINAL_REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
            $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
        }
    }

    //todo remove postpone
    protected function parseRequestBody() {
        if (isset($_SERVER['CONTENT_TYPE'])
            && $_SERVER['CONTENT_TYPE'] === 'application/json'
        ) {
            JsonRequestBodyParser::parse();
        }
    }

    protected function initializeRouter() {
        $this->router = new Router($this);
    }
}
