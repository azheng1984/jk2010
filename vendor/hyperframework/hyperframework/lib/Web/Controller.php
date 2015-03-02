<?php
namespace Hyperframework\Web;

use Generator;
use Closure;
use Exception;
use InvalidArgumentException;
use LogicException;
use UnexpectedValueException;
use Hyperframework\Common\Config;
use Hyperframework\Common\NotSupportedException;
use Hyperframework\Common\ClassNotFoundException;

abstract class Controller {
    private $app;
    private $filterChain = [];
    private $isFilterChainReversed = false;
    private $isQuitFilterChainMethodCalled = false;
    private $isQuitMethodCalled = false;
    private $actionResult;
    private $view;
    private $isViewEnabled = true;

    public function __construct($app) {
        if ($app === null) {
            throw new InvalidArgumentException(
                "Argument 'app' cannot be null."
            );
        }
        $this->app = $app;
    }

    public function run() {
        try {
            $this->runBeforeFilters();
            $this->handleAction();
            $this->runAfterFilters();
        } catch (Exception $e) {
            $this->quitFilterChain($e);
        }
        $this->finalize();
    }

    public function getApp() {
        if ($this->app === null) {
            throw new LogicException(
                "App cannot be null, constructor method of class"
                    . " '" . __CLASS__ . "' is not called."
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

    public function hasRouteParam($name) {
        $this->getRouter()->hasParam($name);
    }

    public function getViewFormat() {
        return $this->getRouteParam('format');
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
            $router = $this->getRouter();
            $module = (string)$router->getModule();
            if ($module !== '') {
                $name = $module;
            } else {
                $name = '';
            }
            $controller = (string)$router->getController();
            if ($controller === '') {
                throw new UnexpectedValueException(
                    'Controller cannot be empty.'
                );
            }
            $action = (string)$router->getAction();
            if ($action === '') {
                throw new UnexpectedValueException('Action cannot be empty.');
            }
            $name .= $controller . '/' . $action;
            return ViewPathBuilder::build($name, $this->getViewFormat());
        }
        return $this->view;
    }

    public function renderView() {
        $view = $this->getView();
        if (is_object($view)) {
            $view->render($this->getActionResult());
        } elseif (is_string($view) === false) {
            throw new UnexpectedValueException(
                "View must be a string or an object, "
                    . gettype($view) . " given."
            );
        }
        $path = $view;
        if ($path === '') {
            throw new UnexpectedValueException('View path cannot be empty.');
        }
        $viewModel = $this->getActionResult();
        if ($viewModel !== null && is_array($viewModel) === false) {
            throw new UnexpectedValueException(
                'View model must be an array, '
                    . gettype($viewModel) . ' given.'
            );
        }
        $view = ViewFactory::createView($viewModel);
        $view->render($path);
    }

    public function getActionResult() {
        return $this->actionResult;
    }

    public function setActionResult($actionResult) {
        $this->actionResult = $actionResult;
    }

    public function quit() {
        if ($this->isQuitMethodCalled) {
            $class = get_called_class();
            throw new LogicException(
                "The quit method of $class cannot be called more than once."
            );
        }
        $this->isQuitMethodCalled = true;
        $this->quitFilterChain();
        $this->finalize();
        $app = $this->getApp();
        $app->quit();
    }

    public function redirect($url, $statusCode = 302) {
        header('Location: ' . $url, true, $statusCode);
        $this->quit();
    }

    public function addBeforeFilter($filter, array $options = null) {
        $this->addFilter('before', $filter, $options);
    }

    public function addAfterFilter($filter, array $options = null) {
        $this->addFilter('after', $filter, $options);
    }

    public function addAroundFilter($filter, array $options = null) {
        if (version_compare(phpversion(), '5.5.0', '<')) {
            throw new NotSupportedException(
                'Around filter requires PHP version 5.5 or later.'
            );
        }
        $this->addFilter('around', $filter, $options);
    }

    protected function handleAction() {
        $router = $this->getRouter();
        $method = $router->getActionMethod();
        if ($method == '') {
            throw new UnexpectedValueException(
                'Action method cannot be empty.'
            );
        }
        if (method_exists($this, $method)) {
            $actionResult = $this->$method();
            $this->setActionResult($actionResult);
        }
        if ($this->isViewEnabled()) {
            $this->renderView();
        }
    }

    protected function finalize() {}

    private function runBeforeFilters() {
        foreach ($this->filterChain as &$config) {
            $type = $config['type'];
            if ($type === 'before' || $type === 'around') {
                $this->runFilter($config);
            }
        }
    }

    private function runAfterFilters() {
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

    private function runFilter(array &$config, $shouldReturnResult = false) {
        $result = null;
        if (is_string($config['filter'])) {
            $class = $config['filter'];
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Action filter class '$class' does not exist."
                );
            }
            $filter = new $class;
            $result = $filter->run($this);
        } elseif ($config['type'] === 'yielded') {
            $result = $config['filter']->next();
            $config['type'] = 'closed';
        } else {
            $function = $config['filter'];
            $result = $function();
        }
        if ($config['type'] === 'around') {
            if ($result instanceof Generator === false) {
                $result = false;
            } else {
                $config['type'] = 'yielded';
                $config['filter'] = $result;
                $result = null;
            }
        }
        if ($shouldReturnResult === false && $result === false) {
            $this->quit();
        }
        return $result;
    }

    private function addFilter($type, $filter, array $options = null) {
        if (is_string($filter)) {
            if ($filter === '') {
                throw new ActionFilterException(
                    'The value of action filter cannot be an empty string.'
                );
            }
        } elseif (is_object($filter) === false
            || $filter instanceof Closure === false
        ) {
            $type = gettype($filter);
            if ($type === 'object') {
                $type = get_class($filter);
            }
            throw new ActionFilterException(
                "The value of action filter must be a closure or a class name,"
                    . " $type given."
            );
        }
        $config = [
            'type' => $type, 'filter' => $filter, 'options' => $options
        ];
        $action = (string)$this->getRouter()->getAction();
        if ($action === '') {
            throw new UnexpectedValueException('Action cannot be empty.');
        }
        if ($options === null) {
            $this->filterChain[] = $config;
            return;
        }
        if (isset($options['actions'])) {
            if (is_array($options['actions']) === false) {
                $type = gettype($options['actions']);
                throw new ActionFilterException(
                    "Option 'actions' must be an array, $type given."
                );
            } elseif (in_array($action, $options['actions']) === false) {
                return;
            }
        }
        if (isset($options['ignored_actions'])) {
            if (is_array($options['ignored_actions']) === false) {
                $type = gettype($options['ignored_actions']);
                throw new ActionFilterException(
                    "Option 'ignored_actions' must be an array, $type given."
                );
            } elseif (in_array($action, $options['ignored_actions'])) {
                return;
            }
        }
        if (isset($options['prepend']) && $options['prepend'] === true) {
            array_unshift($this->filterChain, $config);
        } else {
            $this->filterChain[] = $config;
        }
    }

    private function quitFilterChain($exception = null) {
        if ($this->isQuitFilterChainMethodCalled === false) {
            $this->isQuitFilterChainMethodCalled = true;
            $shouldRunYieldedFiltersOnly = $exception === null
                || $this->isFilterChainReversed === false;
            $shouldRunAfterFilter = false;
            if ($this->isFilterChainReversed === false) {
                $this->filterChain = array_reverse($this->filterChain);
                $this->isFilterChainReversed = true;
            }
            foreach ($this->filterChain as &$filterConfig) {
                if ($filterConfig['type'] === 'yielded' ||
                    ($shouldRunAfterFilter && $filterConfig['type'] === 'after')
                ) {
                    try {
                        if ($exception !== null) {
                            $result =
                                $filterConfig['filter']->throw($exception);
                            $shouldRunAfterFilter = $result !== false
                                && $shouldRunYieldedFiltersOnly === false;
                            $exception = null;
                        } else {
                            $result = $this->runFilter($filterConfig, true); 
                            if ($result === false) {
                                $shouldRunYieldedFiltersOnly = true;
                                $shouldRunAfterFilter = false;
                            }
                        }
                    } catch (Exception $exception) {
                        $shouldRunAfterFilter = false;
                    }
                }
            }
        }
        if ($exception !== null) {
            throw $exception;
        }
    }
}
