<?php
namespace Hyperframework\Web;

use Closure;
use InvalidArgumentException;
use Hyperframework\Common\Config;
use Hyperframework\Common\NamespaceCombiner;
use Hyperframework\Common\InvalidOperationException;

abstract class Router {
    private $app;
    private $params = [];
    private $module;
    private $moduleNamespace;
    private $controller;
    private $controllerClass;
    private $controllerRootNamespace;
    private $action;
    private $actionMethod;
    private $requestPath;
    private $shouldMatchScope = false;
    private $isMatched = false;

    public function __construct($app) {
        if ($app === null) {
            throw new InvalidArgumentException(
                "Argument 'app' cannot be null."
            );
        }
        $this->app = $app;
        $result = $this->execute();
        $this->parseResult($result);
        if ($this->isMatched() === false) {
            throw new NotFoundException;
        }
    }

    public function getParam($name) {
        return $this->params[$name];
    }

    public function getParams() {
        return $this->params;
    }

    public function hasParam($name) {
        return isset($this->params[$name]);
    }

    public function getModule() {
        return $this->module;
    }

    public function getModuleNamespace() {
        if ($this->moduleNamespace !== null) {
            return $this->moduleNamespace;
        }
        $module = (string)$this->getModule();
        if ($module === '') {
            return;
        }
        $tmp = str_replace(
            ' ', '\\', ucwords(str_replace('/', ' ', $module))
        );
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $tmp)));
    }

    public function getController() {
        if ($this->controller === null) {
            return 'index';
        }
        return $this->controller;
    }

    public function getControllerClass() {
        $class = null;
        if ($this->controllerClass !== null) {
            $class = (string)$this->controllerClass;
            if ($class === '') {
                return $this->controllerClass;
            }
            if ($class[0] === '\\') {
                return substr($class, 1);
            }
        } else {
            $controller = (string)$this->getController();
            if ($controller === '') {
                return;
            } else {
                $tmp = ucwords(str_replace('_', ' ', $controller));
                $class = str_replace(' ', '', $tmp) . 'Controller';
            }
        }
        $moduleNamespace = (string)$this->getModuleNamespace();
        if ($moduleNamespace !== '' && $moduleNamespace !== '\\') {
            NamespaceCombiner::prepend($class, $moduleNamespace);
        }
        $rootNamespace = (string)$this->getControllerRootNamespace();
        if ($rootNamespace !== '' && $rootNamespace !== '\\') {
            NamespaceCombiner::prepend($class, $rootNamespace);
        }
        $this->controllerClass = '\\' . $class;
        return $class;
    }

    public function getAction() {
        if ($this->action === null) {
            return 'show';
        }
        return $this->action;
    }

    public function getActionMethod() {
        if ($this->actionMethod !== null) {
            return $this->actionMethod;
        }
        $action = (string)$this->getAction();
        if ($action === '') {
            return;
        }
        $tmp = str_replace(' ', '', ucwords(str_replace('_', ' ', $action)));
        return 'do' . $tmp . 'Action';
    }

    abstract protected function execute();

    protected function match($pattern, array $options = null) {
        if (is_string($pattern) === false) {
            throw new InvalidArgumentException(
                "Argument 'pattern' must be a string, "
                    . gettype($pattern) . ' given.'
            );
        }
        if ($this->isMatched()) {
            throw new RoutingException('Already matched.');
        }
        if ($options !== null) {
            if (isset($options['methods'])) {
                if (is_string($options['methods'])) {
                    $options['methods'] = [$options['methods']];
                }
                $isMethodAllowed = false;
                foreach ($options['methods'] as $method) {
                    if (strtoupper($method) === $_SERVER['REQUEST_METHOD']) {
                        $isMethodAllowed = true;
                        break;
                    }
                }
                if ($isMethodAllowed === false) {
                    return false;
                }
            }
        }
        if (strpos($pattern, '#') !== false) {
            throw new RoutingException(
                "Invalid pattern '$pattern', character '#' is not allowed."
            );
        }
        $hasOptionalSegment = strpos($pattern, '(') !== false;
        $hasDynamicSegment = strpos($pattern, ':') !== false;
        $hasWildcardSegment = strpos($pattern, '*') !== false;
        $hasFormat = isset($options['formats']);
        $path = trim($this->getRequestPath(), '/');
        $pattern = trim($pattern, '/');
        if ($hasFormat && is_array($options['formats']) === false) {
            $options['formats'] = [$options['formats']];
        }
        $originalPattern = $pattern;
        if ($hasFormat === false
            && $hasOptionalSegment === false
            && $hasWildcardSegment === false
            && $hasDynamicSegment === false
            && $this->shouldMatchScope ===  false
        ) {
            if ($path === $pattern) {
                if (isset($options['extra'])) {
                    $isMatched =
                        $this->verifyExtraRules($options['extra']);
                    if ($isMatched === false) {
                        return false;
                    }
                }
                $this->setMatchStatus(true);
                return true;
            }
            return false;
        }
        if ($hasOptionalSegment) {
            $pattern = str_replace(')', ')?', $pattern);
        }
        if ($hasDynamicSegment) {
            $pattern = str_replace(':', '#:', $pattern);
            if ($options !== null) {
                foreach ($options as $key => $value) {
                    if ($key[0] === ':') {
                        $pattern = preg_replace(
                            '#\{?\#' . $key . '(?=([^a-zA-Z0-9_]|$))\}?#',
                            '(?<' . substr($key, 1) . '>' . $value . '?)',
                            $pattern
                        );
                    }
                }
            }
            if (strpos($pattern, '#:') !== false) {
                $pattern = preg_replace(
                    '#\{?\#:([a-zA-Z0-9_]+)\}?#',
                    '(?<\1>[^/]+?)',
                    $pattern
                );
            }
        }
        if ($hasWildcardSegment) {
            $pattern = preg_replace(
                '#\{?\*([a-zA-Z0-9_]+)\}?#',
                '(?<\1>.+?)',
                $pattern
            );
        }
        $formatPattern = null;
        $isOptionalFormat = isset($options['default_format']);
        if ($hasFormat) {
            if ($isOptionalFormat) {
                $formatPattern = '(\.(?<format>[0-9a-zA-Z]+?))?';
            } else {
                $formatPattern = '\.(?<format>[0-9a-zA-Z]+?)';
            }
        }
        if ($this->shouldMatchScope) {
            $pattern = '#^' . $pattern . '($|/(.*?)$)#';
            //echo $pattern;
        } else {
            $pattern = '#^' . $pattern . $formatPattern . '$#';
        }
        $result = preg_match($pattern, $path, $matches);
        if ($result === false) {
            throw new RoutingException("Invalid pattern '$originalPattern'.");
        }
        if ($result === 1) {
            if ($hasFormat) {
                if (isset($matches['format']) === false) {
                    if (isset($options['default_format'])) {
                        $this->setParam(
                            'format', $options['default_format']
                        );
                    } else {
                        return false;
                    }
                } elseif (
                    in_array($matches['format'], $options['formats']) === false
                ) {
                    return false;
                }
            }
            $pattern = '#^[a-zA-Z_][a-zA-Z0-9_]*$#';
            if (isset($matches['module'])
                && isset($options[':module']) === false
            ) {
                $module = str_replace('/', '0', $matches['module']);
                if (preg_match($pattern, $module) === 0) {
                    return false;
                }
            }
            if (isset($matches['controller'])
                && isset($options[':controller']) === false
            ) {
                if (preg_match($pattern, $matches['controller']) === 0) {
                    return false;
                }
            }
            if (isset($matches['action'])
                && isset($options[':action']) === false
            ) {
                if (preg_match($pattern, $matches['action']) === 0) {
                    return false;
                }
            }
            if (isset($options['extra'])) {
                $tmp = $this->verifyExtraRules(
                    $options['extra'], $matches
                );
                if ($tmp === false) {
                    return false;
                }
            }
            if ($this->shouldMatchScope) {
                return end($matches);
            }
            $this->setMatches($matches);
            $this->setMatchStatus(true);
            return true;
        }
        return false;
    }

    protected function matchScope($path, Closure $callback) {
        if (is_string($path) === false) {
            throw new InvalidArgumentException(
                "Argument 'path' must be a string, "
                    . gettype($path) . ' given.'
            );
        }
        $this->shouldMatchScope = true;
        $childPath = $this->match($path);
        $this->shouldMatchScope = false;
        if ($childPath === false) {
            return false;
        }
        $previousPath = $this->getRequestPath();
        $this->setRequestPath(trim($childPath, '/'));
        $result = $callback();
        $this->parseResult($result);
        $this->setRequestPath($previousPath);
        return $this->isMatched();
    }

    protected function matchResource($pattern, array $options = null) {
        //todo check type
        // actions
        // default_actions
        // ignored_actions
        // extra_actions
        $defaultActions = null;
        if (isset($options['default_actions'])) {
            $defaultActions = $options['default_actions'];
            //process default int key item
        } else {
            $defaultActions = [
                'show' => ['GET', '/'],
                'new' => ['GET', 'new'],
                'update' => ['PATCH | PUT', '/'],
                'create' => ['POST', '/'],
                'delete' => ['DELETE', '/'],
                'edit' => ['GET', 'edit'],
            ];
        }
        $actions = null;
        if (isset($options['actions'])) {
            $actions = $options['actions'];
            foreach ($actions as $key => $value) {
                if (is_int($key)) {
                    if (is_string($value) === false) {
                        throw new RoutingException(
                            'Action name must be a string, '
                                . gettype($value) . ' given.'
                        );
                    }
                    if (isset($defaultActions[$value])) {
                        $actions[$value] = $defaultActions[$value];
                    } else {
                        $actions[$value] = [];
                    }
                }
            }
            unset($options['actions']);
        } else {
            $actions = $defaultActions;
            if ($actions !== null) {
                foreach ($actions as $key => $value) {
                    if (is_int($key)) {
                        unset($actions[$key]);
                        if (is_string($value) === false) {
                            throw new RoutingException(
                                'Action name must be a string, '
                                    . gettype($value) . ' given.'
                            );
                        }
                        if (isset($defaultActions[$value])) {
                            $actions[$value] = $defaultActions[$value];
                        } else {
                            $actions[$value] = [];
                        }
                    }
                }
                if (isset($options['ignored_actions'])) {
                    foreach ($options['ignored_actions'] as $item) {
                        unset($actions[$item]);
                    }
                    unset($options['ignored_actions']);
                }
            }
        }
        if (isset($options['extra_actions'])) {
            if ($actions === null) {
                $actions = [];
            }
            foreach ($options['extra_actions'] as $key => $value) {
                if (is_int($key)) {
                    if (is_string($value) === false) {
                        throw new RoutingException(
                            'Action name must be a string, '
                                . gettype($value) . ' given.'
                        );
                    }
                    if (isset($defaultActions[$value])) {
                        $actions[$value] = $defaultActions[$value];
                    } else {
                        $actions[$value] = [];
                    }
                } else {
                    $action[$key] = $value;
                }
            }
            if ($actions === null) {
                $actions = $options['extra_actions'];
            } else {
                $actions = array_merge($actions, $options['extra_actions']);
            }
            unset($options['extra_actions']);
        }
        unset($options['default_actions']);
        if ($actions === null || count($actions) === 0) {
            return false;
        }
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $pattern = rtrim($pattern, '/');
        $action = null;
        foreach ($actions as $key => $value) {
            $action = $key;
            if (is_array($value) === false) {
                $value = [$value];
            }
            if (isset($value[0])) {
                if (strpos($value[0], '|') !== false) {
                    $tmps = explode('|', $value[0]);
                    $value[0] = [];
                    foreach ($tmps as $tmp) {
                        $value[0][] = strtoupper(trim($tmp));
                    }
                } else {
                    $value[0] = [strtoupper($value[0])];
                }
            } else {
                if (isset($value[1])) {
                    //throw e: method missing
                }
                $value[0] = ['GET'];
            }
            if (in_array($requestMethod, $value[0]) === false) {
                continue;
            }
            unset($value[0]);
            if (isset($value[1])) {
                $suffix = $value[1];
                unset($value[1]);
            } else {
                $suffix = $key;
            }
            $actionOptions = null;
            if (count($value) !== 0) {
                $actionOptions = $value;
                $actionExtra = null;
                if (isset($actionOptions['extra'])) {
                    $actionExtra = $actionOptions['extra'];
                }
                if ($options !== null) {
                    $actionOptions = $options + $actionOptions;
                }
                if (isset($options['extra']) && $actionExtra !== null) {
                    $extra = $options['extra'];
                    if (is_array($extra) === false) {
                        $extra = [$extra];
                    }
                    if (is_array($actionExtra)) {
                        $extra = array_merge($extra, $actionExtra);
                    } else {
                        $extra[] = $actionExtra;
                    }
                    $actionOptions['extra'] = $extra;
                }
            } else {
                $actionOptions = $options;
            }
            $actionPattern = $pattern;
            $suffix = trim($suffix, '/');
            if ($suffix !== '') {
                $actionPattern .= '/' . $suffix;
            }
            if ($this->match($actionPattern, $actionOptions)) {
                $action = $key;
                break;
            }
        }
        if ($this->isMatched()) {
            $controller = $pattern;
            if (($slashPosition = strrpos($pattern, '/')) !== false) {
                $controller = substr($pattern, $slashPosition + 1);
            }
            $this->setController($controller);
            $this->setAction($action);
            return true;
        }
        return false;
    }

    protected function matchResources($pattern, array $options = null) {
        //todo type test
        if (preg_match('#[:*]id($|[/{])#', $pattern) !== 0) {
            throw new RoutingException(
                "Invalid pattern '$pattern', "
                    . "dynamic segment ':id' is reserved."
            );
        }
        $hasOptions = $options !== null;
        if ($hasOptions) {
            if (isset($options['id'])) {
                $options[':id'] = $options['id'];
            } elseif (isset($options[':id'])) {
                throw new RoutingException(
                    "Dynamic segment ':id' is reserved, "
                        . "use option 'id' to change pattern for it."
                );
            } else {
                $options[':id'] = '\d+';
            }
        } else {
            $options = [':id' => '\d+'];
        }
        if ($hasOptions === false
            || isset($options['default_actions']) === false
        ) {
            $defaultOptions = [
                'index' => ['GET', '/'],
                'show' => ['GET', '/', 'belongs_to_element' => true],
                'new' => ['GET', 'new'],
                'edit' => ['GET', 'edit', 'belongs_to_element' => true],
                'create' => ['POST', '/'],
                'update' => [
                    'PATCH | PUT', '/', 'belongs_to_element' => true
                ],
                'delete' => ['DELETE', '/', 'belongs_to_element' => true],
            ];
        } else {
            //check is array
            //process default int key item
            $defaultAction = $options['default_actions'];
        }
        // actions //same
        // default_actions //same
        // ignored_actions //same
        // collection_actions => actions
        // extra_collection_actions => extra_actions
        // element_actions => actions
        // extra_element_actions //extra_actions
        if (isset($options['collection_actions'])) {
            if ($options['collection_actions'] === false) {
                if (isset($options['actions']) === false) {
                    if (isset($options['element_acitons']) === false) {
                        foreach ($defaultActions as $key => $value) {
                            if (isset($value['belongs_to_element'])
                                && $value['belongs_to_element'] === true
                            ) {
                                $options['actions'][$key] = $value;
                            }
                        }
                    }
                }
            } else {
                if (isset($options['actions']) === false) {
                    $options['actions'] = $options['collection_actions'];
                } else {
                    $options['actions'] = array_merge(
                        $options['actions'], $options['collection_actions']
                    );
                }
            }
        }
        if (isset($options['element_actions'])) {
            if ($options['element_actions'] === false) {
                if (isset($options['actions']) === false) {
                    if (isset($options['collection_acitons']) === false) {
                        foreach ($defaultActions as $key => $value) {
                            if (isset($value['belongs_to_element']) === false
                                || $value['belongs_to_element'] !== true
                            ) {
                                $options['actions'][$key] = $value;
                            }
                        }
                    }
                }
            } else {
                $actions = $this->convertElementActionsToCollectionActions(
                    $options['element_actions'], $defaultActions
                );
                if (isset($options['actions']) === false) {
                    $options['actions'] = $actions;
                } else {
                    $options['actions'] = array_merge(
                        $options['actions'], $actions
                    );
                }
            }
        }
        if (isset($options['actions'])) {
            $options['actions'] =
                $this->convertElementActionsToCollectionActions(
                    $options['actions'], $defaultActions, true
                );
        }
        unset($options['collection_actions']);
        unset($options['element_actions']);
        if (isset($options['extra_actions'])) {
            //exception
        }
        if (isset($options['extra_collection_actions'])) {
            $options['extra_actions'] = $options['extra_collection_actions'];
        }
        if (isset($options['extra_element_actions'])) {
            $actions = $this->convertElementActionsToCollectionActions(
                $options['extra_element_actions'], $defaultActions
            );
            if (isset($options['extra_actions'])) {
                $options['extra_actions'] = array_merge(
                    $options['extra_actions'], $actions
                );
            }
        }
        unset($options['extra_collection_actions']);
        unset($options['extra_element_actions']);
        $options['default_actions'] = 
            $this->convertElementActionsToCollectionActions(
                $defaultOptions, null, true
            );
        return $this->matchResource($pattern, $options);
    }

    private function convertElementActionsToCollectionActions(
        array $actions, array $defaultActions = null, $isMixed = false
    ) {
        //todo type test
        $result = [];
        foreach ($actions as $key => $value) {
            if (is_int($key)) {
                if (isset($defaultActions[$value])
                    && isset($defaultActions[$value]['belongs_to_element'])
                    && $defaultActions[$value]['belongs_to_element'] === true
                ) {
                    $key = $value;
                    $value = $defaultActions[$value];
                    if ($isMixed === false) {
                        unset($value['belongs_to_element']);
                    }
                } else {
                    if ($isMixed) {
                        $result[$key] = $value;
                        continue;
                    }
                    if (is_string($value) === false) {
                        throw new RoutingException(
                            'Action name must be a string, '
                                . gettype($value) . ' given.'
                        );
                    }
                    $key = $value;
                    $value = ['GET', ':id/' . ltrim($value, '/')];
                    $result[$key] = $value;
                    continue;
                }
            }
            if ($isMixed) {
                if (isset($value['belongs_to_element']) === false
                    || $value['belongs_to_element'] !== true
                ) {
                    $result[$key] = $value;
                    continue;
                } else {
                    unset($value['belongs_to_element']);
                }
            }
            if (is_string($value)) {
                $value = ['GET', ':id/' . ltrim($value, '/')];
            } else {
                //todo check value is array
                if (isset($value[1])) {
                    $value[1] = ':id/' . ltrim($value[1], '/');
                } else {
                    $value[1] = ':id/' . ltrim($key, '/');
                }
            }
            $result[$key] = $value;
        }
        return $result;
    }

    protected function matchGet($pattern, array $options = null) {
        $options['methods'] = 'GET';
        return $this->match($pattern, $options);
    }

    protected function matchPost($pattern, array $options = null) {
        $options['methods'] = 'POST';
        return $this->match($pattern, $options);
    }

    protected function matchPut($pattern, array $options = null) {
        $options['methods'] = 'PUT';
        return $this->match($pattern, $options);
    }

    protected function matchPatch($pattern, array $options = null) {
        $options['methods'] = 'PATCH';
        return $this->match($pattern, $options);
    }

    protected function matchDelete($pattern, array $options = null) {
        $options['methods'] = 'DELETE';
        return $this->match($pattern, $options);
    }

    protected function redirect($url, $statusCode = 302) {
        $this->app->redirect($url, $statusCode);
    }

    protected function isMatched() {
        return $this->isMatched;
    }
    
    protected function setMatchStatus($isMatched) {
        $this->isMatched = $isMatched;
    }

    protected function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    protected function removeParam($name) {
        unset($this->params[$name]);
    }

    protected function setModule($value) {
        $this->module = (string)$value;
    }

    protected function setModuleNamespace($value) {
        $this->moduleNamespace = (string)$value;
    }

    protected function setController($value) {
        $this->controller = (string)$value;
    }

    protected function setControllerClass($value) {
        $this->controllerClass = (string)$value;
    }

    protected function getControllerRootNamespace() {
        if ($this->controllerRootNamespace === null) {
            $namespace = 'Controllers';
            $rootNamespace = Config::getAppRootNamespace();
            if ($rootNamespace !== '' && $rootNamespace !== '\\') {
                NamespaceCombiner::prepend($namespace, $rootNamespace);
            }
            $this->controllerRootNamespace = $namespace;
        }
        return $this->controllerRootNamespace;
    }

    protected function setControllerRootNamespace($value) {
        $this->controllerRootNamespace = $value;
    }

    protected function setAction($value) {
        $this->action = (string)$value;
    }

    protected function setActionMethod($value) {
        $this->actionMethod = (string)$value;
    }

    protected function getRequestPath() {
        if ($this->requestPath === null) {
            $path = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
            if ($path === '') {
                $path = '/';
            } elseif (strpos($path, '//') !== false) {
                $path = preg_replace('#/{2,}#', '/', $path);
            }
            $this->requestPath = '/' . trim($path, '/');
        }
        return $this->requestPath;
    }

    protected function getApp() {
        if ($this->app === null) {
            throw new InvalidOperationException(
                "Constructor method of class '" . __CLASS__ . "' is not called."
            );
        }
        return $this->app;
    }

    private function verifyExtraRules($extra, array $matches = null) {
        if (is_array($extra)) {
            foreach ($extra as $function) {
                if ($function instanceof Closure === false) {
                    $type = gettype($function);
                    if ($type === 'Object') {
                        $type = get_class($function);
                    }
                    throw new RoutingException(
                        'Extra rule must be a Closure, ' . $type . ' given.'
                    );
                }
                if ($function($matches) === false) {
                    return false;
                }
            }
            return true;
        } else {
            if ($extra instanceof Closure === false) {
                $type = gettype($function);
                if ($type === 'Object') {
                    $type = get_class($function);
                }
                throw new RoutingException(
                    'Extra rule must be a Closure, ' . $type . ' given.'
                );
            }
            return $extra($matches) !== false;
        }
    }

    private function setMatches($matches) {
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                if ($key === 'module') {
                    $this->setModule($value);
                } elseif ($key === 'controller') {
                    $this->setController($value);
                } elseif ($key === 'action') {
                    $this->setAction($value);
                } else {
                    $this->setParam($key, $value);
                }
            }
        }
    }

    private function parseResult($value) {
        if ($value === null) {
            return;
        }
        if ($value === false) {
            $this->setMatchStatus(false);
            return;
        }
        if (is_string($value)) {
            if ($value === '') {
                throw new RoutingException(
                    "Invalid router execution result, "
                        . "empty string is not allowed."
                );
            }
            $segments = explode('/', $value);
            switch (count($segments)) {
                case 1:
                    $this->setAction($segments[0]);
                    break;
                case 2:
                    $this->setController($segments[0]);
                    $this->setAction($segments[1]);
                    break;
                default:
                    $this->setAction(array_pop($segments));
                    $this->setController(array_pop($segments));
                    $this->setModule(implode('/', $segments));
            }
        } elseif ($value !== true) {
            throw new RoutingException(
                "Invalid router execution result, "
                    . gettype($value) . " is not allowed."
            );
        }
        $this->setMatchStatus(true);
    }

    private function setRequestPath($value) {
        $this->requestPath = (string)$value;
    }
}
