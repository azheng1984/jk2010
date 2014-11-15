<?php
namespace Hyperframework\Web;

use Exception;
use Generator;
use Closure;

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
        foreach ($this->filterChain as &$filterConfig) {
            $filterType = $filterConfig['type'];
            if ($filterType === 'before' || $filterType === 'around') {
                $this->runFilter($filterConfig);
            }
        }
    }

    final protected function runAfterFilters() {
        if ($this->isFilterChainReversed === false) {
            $this->filterChain = array_reverse($this->filterChain);
            $this->isFilterChainReversed = true;
        }
        foreach ($this->filterChain as &$filterConfig) {
            $filterType = $filterConfig['type'];
            if ($filterType === 'after' || $filterType === 'yielded') {
                $this->runFilter($filterConfig);
            }
        }
    }

    protected function executeAction() {
        $router = $this->getRouter();
        $method = $router->getActionMethod();
        if ($method === null) {
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
            if (isset($value['options']) && isset($value['options']['name'])) {
                if ($value['options']['name'] === $name) {
                    unset($this->filterChain[$key]);
                } else {
                    continue;
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
        $filterConfig = [
            'type' => $type, 'filter' => $filter, 'options' => $options
        ];
        $action = $this->getRouter()->getAction();
        if ($action === null) {
            throw new Exception;
        }
        if ($options === null) {
            $this->filterChain[] = $filterConfig;
            return;
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
            array_unshift($this->filterChain, $filterConfig);
        } else {
            $this->filterChain[] = $filterConfig;
        }
    }

    public function getApp() {
        if ($this->app === null) {
            throw new Exception('99% consturct not called ...');
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
        if ($view === null) {
            $router = $this->getRouter();
            if ($router->getModuleDirectory() !== null) {
                $view .= $this->getModuleDirectory();
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
        $this->disableView();
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
                        $shouldRunAfterFilter =
                            $filterConfig['filter']->throw($exception) !== false
                                && $shouldRunYieldedFiltersOnly === false;
                        $exception = null;
                    } else {
                        $result = null;
                        if ($filterConfig['type'] === 'yielded') {
                            $result = $filterConfig['filter']->next();
                        } else {
                            $result = $this->runFilter($filterConfig, true);
                        }
                        if ($result === false) {
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
