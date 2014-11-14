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

    private function getFilter($config) {
    }

    public function run() {
        $filters = $this->getFilters();
        $action = $this->getRouter()->getAction();
        if ($action === null) {
            throw new Exception;
        }
        $tmps = [];
        foreach ($filters as $key => $filter) {
            $options = null;
            if (isset($filter[2])) {
                $options = $filter[2];
            } else {
                $tmps[] = $filter;
            }
            if (isset($options['ignore_actions'])) {
                if (in_array($action, $options['ignore_actions'])) {
                    continue;
                }
            }
            if (isset($options['actions'])) {
                if (in_array($action, $options['actions']) === false) {
                    continue;
                }
            }
            if (isset($options['prepend']) && $options['prepend'] === true) {
                array_unshift($tmps, $filter);
            }
        }
        $filters = $tmps;
        foreach ($filters as &$filter) {
            switch ($filter[0]) {
                case 'before':
                    if (is_string($filter[1])) {
                        if ($filter[1] === '') {
                            throw new Exception;
                        }
                        if ($filter[1][0] === ':') {
                            $method = substr($filter[1][0], 1);
                            if ($this->$method() === false) {
                                $this->getApp()->quit();
                            }
                            break;
                        }
                        $class = $filter[1];
                        $filter = new $class;
                        if ($filter->run($this) === false) {
                            $this->getApp()->quit();
                        }
                        break;
                    } elseif (is_object($filter[1])) {
                        if ($filter[1] instanceof Closure) {
                            $function = $filter[1];
                            if ($function($this) === false) {
                                $this->getApp()->quit();
                            }
                            break;
                        }
                        if ($filter[1]->run($this)) {
                            $this->getApp()->run();
                        }
                    }
                    throw new Exception;
                case 'around':
                    if (is_string($filter[1])) {
                        if ($filter[1] === '') {
                            throw new Exception;
                        }
                        if ($filter[1][0] === ':') {
                            $method = substr($filter[1][0], 1);
                            $generator = $this->$method();
                            if ($generator instanceof Generator === false) {
                                throw new Exception;
                            }
                            if ($generator->next() === false) {
                                $this->getApp()->quit();
                            }
                            $filter[1] = $generator;
                            break;
                        }
                        $class = $filter[1];
                        $object = new $class;
                        $generator = $object->run($this);
                        if ($generator instanceof Generator === false) {
                            throw new Exception;
                        }
                        if ($generator->next() === false) {
                            $this->getApp()->quit();
                        }
                        $filter[1] = $generator;
                        break;
                    } elseif (is_object($filter[1])) {
                        if ($filter[1] instanceof Closure) {
                            $function = $filter[1];
                            if ($function($this) === false) {
                                $this->getApp()->quit();
                            }
                            break;
                        }
                        if ($filter[1]->run($this)) {
                            $this->getApp()->run();
                        }
                    }
                    throw new Exception;
                }
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
            foreach ($filters as $filter) {
                switch ($filter[0]) {
                    case 'before':
                        break;
                    case 'around':
                        break;
                    case 'after':
                        break;
                }
            }
        }
        foreach ($filters as $filter) {
            switch ($filter[0]) {
                case 'before':
                    break;
                case 'around':
                    break;
                case 'after':
                    break;
            }
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

    public function addAroundFilter($callback, array $options = null) {
        if (version_compare(phpversion(), '5.5.0', '<')) {
            throw new Exception;
        }
        $this->filters[] = ['around', $callback, $options];
    }

    public function removeFilter($name) {
        foreach ($this->filters as $key => $value) {
            if (isset($value[2]) && isset($value[2]['name'])) {
                if ($value[2]['name'] === $name) {
                    unset($this->filters[$key]);
                } else {
                    continue;
                }
            } elseif (is_string($value[0])) {
                if ($value[0] === $name) {
                    unset($this->filters[$key]);
                }
            }
        }
    }

    public function getFilters() {
        $tmps = [];
        foreach ($filters as $key => $filter) {
            $options = null;
            if (isset($filter[2])) {
                $options = $filter[2];
            } else {
                $tmps[] = $filter;
            }
            if (isset($options['ignore_actions'])) {
                if (in_array($action, $options['ignore_actions'])) {
                    continue;
                }
            }
            if (isset($options['actions'])) {
                if (in_array($action, $options['actions']) === false) {
                    continue;
                }
            }
            if (isset($options['prepend']) && $options['prepend'] === true) {
                array_unshift($tmps, $filter);
            }
        }
        return $tmps;
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
