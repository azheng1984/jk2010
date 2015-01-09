<?php
namespace Hyperframework\Web;

use Generator;
use Closure;
use Hyperframework\Common\ViewTemplate;
use Exception;
use InvalidArgumentException;
use UnexpectedValueException;
use Hyperframework\Common\InvalidOperationException;
use Hyperframework\Common\NotSupportedException;

class Controller {
    private $app;
    private $filterChain = [];
    private $isFilterChainReversed = false;
    private $isFilterChainQuitted = false;
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
    }

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

    protected function handleAction() {
        $router = $this->getRouter();
        $method = $router->getActionMethod();
        if ($method == '') {
            throw new UnexpectedValueException('Action method 不能为空');
        }
        if (method_exists($this, $method)) {
            $actionResult = $this->$method();
            $this->setActionResult($actionResult);
        }
        if ($this->isViewEnabled()) {
            $this->renderView();
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
            throw new NotSupportedException(
                'Around filter 需要 PHP 版本大于等于 5.5'
            );
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

    private function runFilter(array &$config, $shouldReturnResult = false) {
        $result = null;
        if (is_string($config['filter'])) {
            if ($config['filter'] === '') {
                throw new InvalidActionFilterException(
                    'Filter is set to an Empty string.'
                );
            }
            if ($config['filter'][0] === ':') {
                $method = substr($config['filter'], 1);
                $result = $this->$method();
            } else {
                $class = $config['filter'];
                if (class_exists($class) === false) {
                    throw new InvalidActionFilterException(
                        "Filter class '$class' does not exist."
                    );
                }
                $filter = new $class;
                $result = $filter->run($this);
            }
        } elseif (is_object($config['filter'])) {
            if ($config['filter'] instanceof Closure) {
                $function = $config['filter'];
                $result = $function($this);
            } else {
                $result = $config['filter']->run($this);
            }
        } else {
            throw new InvalidActionException(
                "Filter type '"
                    . gettype($config['filter']) . "' is not allowed."
            );
        }
        if ($config['type'] === 'around') {
            if (is_object($result) === false
                || $result instanceof Generator === false
            ) {
                if (is_object($result)) {
                    $type = get_class($result);
                } else {
                    $type = gettype($result);
                }
                throw new InvalidActionFilterException(
                    'Around filter must return a generator, '
                        . $type . ' returned.'
                );
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
        if ($shouldReturnResult === false && $result === false) {
            $this->quit();
        }
        return $result;
    }

    private function addFilter($type, $filter, array $options = null) {
        $config = [
            'type' => $type, 'filter' => $filter, 'options' => $options
        ];
        $action = (string)$this->getRouter()->getAction();
        if ($action === '') {
            throw new UnexpectedValueException('Action 不能为空.');
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
            throw new InvalidOperationException(
                "App is null. Object 没有被正确 construct."
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
            $controller = (string)$router->getController();
            if ($controller === '') {
                throw new UnexpectedValueException('Controller 不能为空.');
            }
            $action = (string)$router->getAction();
            if ($action === '') {
                throw new UnexpectedValueException('Action 不能为空.');
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
        $template = new ViewTemplate($this->getActionResult());
        $view = $this->getView();
        $template->load($view);
        $this->disableView();
        return;
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

    private function quitFilterChain($exception = null) {
        if ($this->isFilterChainQuitted === false) {
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
                            $result =$this->runFilter($filterConfig, true); 
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
            $this->isFilterChainQuitted = true;
        }
        if ($exception !== null) {
            throw $exception;
        }
    }
}
