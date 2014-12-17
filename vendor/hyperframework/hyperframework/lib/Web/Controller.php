<?php
namespace Hyperframework\Web;

use Exception;
use Generator;
use Closure;
use Hyperframework\Common\ViewTemplate;

class Controller {
    private $app;
    private $filterChain = [];
    private $isFilterChainReversed = false;
    private $actionResult;
    private $view;
    private $isViewEnabled = true;

    public function __construct($app) {
        $this->app = $app;
    }

    public function run() {
        try {
            $this->runBeforeFilters();
            $this->executeAction();
            if ($this->isViewEnabled()) {
                $this->renderView();
            }
            $this->runAfterFilters();
        } catch (Exception $e) {
            $this->quitFilterChain($e);
        }
    }

    final protected function runBeforeFilters() {
        foreach ($this->filterChain as &$config) {
            $type = $config['type'];
            if ($type === 'before' || $type === 'around') {
                $this->runFilter($config);
            }
        }
    }

    final protected function runAfterFilters() {
        if ($this->isFilterChainReversed === false) {
            $this->filterChain = array_reverse($this->filterChain);
            $this->isFilterChainReversed = true;
        }
        foreach ($this->filterChain as &$config) {
            $type = $config['type'];
            if ($type === 'after' || $type === 'yielded') {
                $this->runFilter($config);
            }
        }
    }

    protected function executeAction() {
        $router = $this->getRouter();
        $method = $router->getActionMethod();
        if ($method == '') {
            throw new Exception;
        }
        if (method_exists($this, $method)) {
            $actionResult = $this->$method();
            $this->setActionResult($actionResult);
        }
    }

    public function addBeforeFilter($filter, array $options = null) {
        $this->addFilter('before', $filter, $options);
    }

    public function addAfterFilter($filter, array $options = null) {
        $this->addFilter('after', $filter, $options);
    }

    public function addAroundFilter($filter, array $options = null) {
        if (version_compare(phpversion(), '5.5.0', '<')) {
            throw new Exception;
        }
        $this->addFilter('around', $filter, $options);
    }

    public function removeFilter($name) {
        foreach ($this->filterChain as $key => $value) {
            if (isset($value['options']['name'])) {
                if ($value['options']['name'] === $name) {
                    unset($this->filterChain[$key]);
                }
            } elseif (is_string($value['filter'])) {
                if ($value['filter'] === $name) {
                    unset($this->filterChain[$key]);
                }
            }
        }
    }

    private function runFilter(array &$config, $return = false) {
        $result = null;
        if (is_string($config['filter'])) {
            if ($config['filter'] === '') {
                throw new Exception;
            }
            if ($config['filter'][0] === ':') {
                $method = substr($config['filter'], 1);
                $result = $this->$method();
            } else {
                $class = $config['filter'];
                $filter = new $class;
                $result = $filter->run($this);
            }
        } elseif ($config['type'] !== 'yielded'
            && is_object($config['filter'])
        ) {
            if ($config['filter'] instanceof Closure) {
                $function = $config['filter'];
                $result = $function($this);
            } else {
                $result = $config['filter']->run($this);
            }
        } elseif ($config['type'] !== 'yielded') {
            throw new Exception;
        }
        if ($config['type'] === 'around') {
            if ($result instanceof Generator === false) {
                throw new Exception;
            }
            if ($result->current() === false || $result->valid() === false) {
                $result = false;
            } else {
                $config['type'] = 'yielded';
                $config['filter'] = $result;
                $result = null;
            }
        } elseif ($config['type'] === 'yielded') {
            $result = $config['filter']->next();
            $config['type'] = 'closed';
        }
        if ($return === false && $result === false) {
            $this->quit();
        }
        return $result;
    }

    private function addFilter($type, $filter, array $options = null) {
        $config = [
            'type' => $type, 'filter' => $filter, 'options' => $options
        ];
        $action = $this->getRouter()->getAction();
        if ($action == '') {
            throw new Exception;
        }
        if ($options === null) {
            $this->filterChain[] = $config;
            return;
        }
        if (isset($options['ignored_actions'])) {
            if (is_string($options['ignored_actions'])) {
                if ($options['ignored_actions'] === $action) {
                    return;
                }
            } elseif (in_array($action, $options['ignored_actions'])) {
                return;
            }
        }
        if (isset($options['actions'])) {
            if (is_string($options['actions'])) {
                if ($options['actions'] !== $action) {
                    return;
                }
            } elseif (in_array($action, $options['actions']) === false) {
                return;
            }
        }
        if (isset($options['prepend']) && $options['prepend'] === true) {
            array_unshift($this->filterChain, $config);
        } else {
            $this->filterChain[] = $config;
        }
    }

    public function getApp() {
        if ($this->app === null) {
            throw new Exception(
                'app equals to null'
                    . '(may be consturctor of \'Controller\' is not called)'
            );
        }
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
        if ($this->view === null) {
            $view = null;
            $router = $this->getRouter();
            $view = '';
            if ($router->getModule() != '') {
                $view .= $this->getModule();
            }
            $controller = $router->getController();
            if ($controller == '') {
                throw new Exception;
            }
            $action = $router->getAction();
            if ($action == '') {
                throw new Exception;
            }
            $view .=  $controller . '/' . $action;
            $format = $router->hasParam('format');
            if ($format != '') {
                $view .= '.' . $format;
            }
            $view .= '.php';
            $this->view = $view;
        }
        return $this->view;
    }

    public function renderView() {
        $view = $this->getView();
        if (is_object($view)) {
            if (method_exsits($view, 'render')) {
                $view->render();
                $this->disableView();
                return;
            } else {
                throw new Exception;
            }
        }
        if (is_string($view)) {
            $template = new ViewTemplate($this->getActionResult());
            $template->load($view);
            $this->disableView();
            return;
        }
        throw new Exception;
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

    public function quit() {
        $this->quitFilterChain();
        $this->getApp()->quit();
    }

    public function redirect($url, $statusCode = 302) {
        $this->quitFilterChain();
        $this->getApp()->redirect($url, $statusCode);
    }

    final protected function quitFilterChain($exception = null) {
        $shouldRunYieldedFiltersOnly = $exception === null
            || $this->isFilterChainReversed === false;
        $shouldRunAfterFilter = false;
        if ($this->isFilterChainReversed === false) {
            $this->filterChain = array_reverse($this->filterChain);
            $this->isFilterChainReversed = true;
        }
        foreach ($this->filterChain as &$filterConfig) {
            if ($filterConfig['type'] === 'yielded'
                || ($shouldRunAfterFilter && $filterConfig['type'] === 'after')
            ) {
                try {
                    if ($exception !== null) {
                        $result = $filterConfig['filter']->throw($exception);
                        $shouldRunAfterFilter = $result !== false
                            && $shouldRunYieldedFiltersOnly === false;
                        $exception = null;
                    } else {
                        if ($this->runFilter($filterConfig, true) === false) {
                            $shouldRunYieldedFiltersOnly = true;
                        }
                    }
                } catch (Exception $excepition) {
                    $shouldRunAfterFilter = false;
                }
            }
        }
        if ($exception !== null) {
            throw $exception;
        }
    }
}
