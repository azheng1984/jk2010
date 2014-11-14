<?php
namespace Hyperframework\Web;

class Controller {
    private $app;
    private $filters = [];
    private $actionResult;
    private $view;
    private $isViewEnabled = true;

    public function __construct($app) {
        $this->app = $app;
    }

    public function run() {
        $filters = $this->getFilters();
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
        $app = $this->getApp();
        $router = $app->getRouter();
        $actionMethod = $router->getActionMethod();
        if ($actionMethod === null) {
            throw new NotFoundException;
        }
        try {
            $this->executeAction($actionMethod);
            if ($this->isViewEnabled) {
                $this->renderView();
            }
        } catch (Exception $e) {
            //execute after throwing filter
        }
        //execute after returning filter
    }

    public function executeAction($method) {
        if (method_exists($this, $method)) {
            $this->setActionResult($controller->$method());
        }
    }

    public function addBeforeFilter($callback, array $options = null) {
        // options
        // prepend
        // name
        //return false; equals $this->app->quit();
    }

    public function addAfterFilter($callback, array $options = null) {
    }

    public function addAfterThrowingFilter($callback, array $options = null) {
    }

    public function addAfterReturningFilter($callback, array $options = null) {
    }

    public function addAroundFilter($callback, array $options = null) {
        if (version_compare(phpversion(), '5.5.0', '<')) {
            throw new Exception;
        }
        $this->filters[] = ['around', $callback, $options];
    }

    public function removeFilter($name) {
    }

    public function getFilters() {
        return $this->filters;
    }

    public function getApp() {
        return $this->app;
    }

    public function getRouter() {
        return $this->getApp()->getRouter();
    }

    public function getRouteParam($name) {
        $this->getRouter()->getParam($name);
    }

    public function getRouteParams() {
        $this->getRouter()->getParams();
    }

    public function setRouteParam($name, $value) {
        $this->getRouter()->setParam($name, $value);
    }

    public function removeRouteParam($name) {
        $this->getRouter()->removeParam($name);
    }

    public function hasRouteParam($name) {
        $this->getRouter()->hasParam($name);
    }

    public function disableView() {
        $this->isViewEnabled = false;
    }

    public function enableView() {
        $this->isViewEnabled = true;
    }

    public function isViewEnabled() {
        return $this->isViewEnabled;
    }

    public function setView($value) {
        $this->view = $value;
    }

    public function getView() {
        return $this->view;
    }

    public function renderView() {
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
        $template = new ViewTemplate($this->getActionResult());
        $template->render($view);
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
        $this->getApp()->redirect($url, $statusCode);
    }
}
