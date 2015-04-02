<?php
namespace Hyperframework\Web;

use Generator;
use Closure;
use Exception;
use InvalidArgumentException;
use LogicException;
use UnexpectedValueException;
use Hyperframework\Common\Config;
use Hyperframework\Common\InvalidOperationException;
use Hyperframework\Common\NotSupportedException;
use Hyperframework\Common\ClassNotFoundException;

abstract class Controller {
    private $app;
    private $filterChain = [];
    private $isFilterChainReversed = false;
    private $isQuitFilterChainMethodCalled = false;
    private $isQuitMethodCalled = false;
    private $isRunMethodCalled = false;
    private $actionResult;
    private $view;
    private $isViewEnabled = true;

    /**
     * @param IApp $app
     */
    public function __construct(IApp $app) {
        $this->app = $app;
    }

    public function run() {
        if ($this->isRunMethodCalled) {
            throw new InvalidOperationException(
                'The run method of ' . __CLASS__
                    . ' cannot be called more than once.'
            );
        }
        $this->isRunMethodCalled = true;
        try {
            $this->runBeforeFilters();
            $this->handleAction();
            $this->runAfterFilters();
        } catch (Exception $e) {
            $this->quitFilterChain($e);
        }
        $this->finalize();
    }

    /**
     * @return IApp
     */
    public function getApp() {
        if ($this->app === null) {
            throw new LogicException(
                "The app equals null, the constructor method of class '"
                    . __CLASS__ . "' is not called."
            );
        }
        return $this->app;
    }

    /**
     * @return IRouter
     */
    public function getRouter() {
        return $this->getApp()->getRouter();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getRouteParam($name) {
        return $this->getRouter()->getParam($name);
    }

    /**
     * @return array
     */
    public function getRouteParams() {
        return $this->getRouter()->getParams();
    }

    /**
     * @return boolean
     */
    public function hasRouteParam($name) {
        return $this->getRouter()->hasParam($name);
    }

    /**
     * @return string
     */
    public function getOutputFormat() {
        return $this->getRouteParam('format');
    }

    public function disableView() {
        $this->isViewEnabled = false;
    }

    public function enableView() {
        $this->isViewEnabled = true;
    }

    /**
     * @return boolean
     */
    public function isViewEnabled() {
        return $this->isViewEnabled;
    }

    /**
     * @param mixed $view
     */
    public function setView($view) {
        $this->view = $view;
    }

    /**
     * @return mixed
     */
    public function getView() {
        if ($this->view === null) {
            $router = $this->getRouter();
            $module = (string)$router->getModule();
            if ($module !== '') {
                $name = $module . '/';
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
            return ViewPathBuilder::build($name, $this->getOutputFormat());
        }
        return $this->view;
    }

    public function renderView() {
        $view = $this->getView();
        if (is_object($view)) {
            $view->render($this->getActionResult());
            return;
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

    /**
     * @return mixed
     */
    public function getActionResult() {
        return $this->actionResult;
    }

    /**
     * @param mixed
     */
    public function setActionResult($actionResult) {
        $this->actionResult = $actionResult;
    }

    public function quit() {
        if ($this->isQuitMethodCalled) {
            throw new InvalidOperationException(
                "The quit method of class '" . __CLASS__
                    . "' cannot be called more than once."
            );
        }
        $this->isQuitMethodCalled = true;
        $this->quitFilterChain();
        $this->finalize();
        $app = $this->getApp();
        $app->quit();
    }

    /**
     * @param string $url
     * @param int $statusCode
     */
    public function redirect($url, $statusCode = 302) {
        Response::setHeader('Location: ' . $url, true, $statusCode);
        $this->quit();
    }

    /**
     * @param string|Closure $filter
     * @param array $options
     */
    public function addBeforeFilter($filter, $options = null) {
        $this->addFilter('before', $filter, $options);
    }

    /**
     * @param string|Closure $filter
     * @param array $options
     */
    public function addAfterFilter($filter, $options = null) {
        $this->addFilter('after', $filter, $options);
    }

    /**
     * @param string|Closure $filter
     * @param array $options
     */
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

    /**
     * @param array &$config
     * @param boolean $shouldReturnResult
     * @return mixed
     */
    private function runFilter(&$config, $shouldReturnResult = false) {
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

    /**
     * @param string $type
     * @param string|Closure $filter
     * @param array $options
     */
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

    /**
     * @param Exception $exception
     */
    private function quitFilterChain(Exception $exception = null) {
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
