<?php
namespace Hyperframework\Web;

use Hyperframework;
use Hyperframework\Common\Config;
use Hyperframework\Common\NamespaceCombiner;
use InvalidArgumentException;

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
    private $scopeFormatStack = [];
    private $scopeMatchStack = [];
    private $shouldMatchScope = false;
    private $isMatched = false;

    public function __construct($app) {
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

    public function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    public function removeParam($name) {
        unset($this->params[$name]);
    }

    public function hasParam($name) {
        return isset($this->params[$name]);
    }

    public function getModule() {
        return $this->module;
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

    protected function isMatched() {
        return $this->isMatched;
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

    protected function getModuleNamespace() {
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

    protected function setMatchStatus($isMatched) {
        $this->isMatched = $isMatched;
    }

    protected function redirect($url, $statusCode = 302) {
        $this->app->redirect($url, $statusCode);
    }

    protected function getApp() {
        return $this->app;
    }

    protected function match($pattern, array $options = null) {
        if ($this->isMatched()) {
            throw new RoutingException('Previous pattern is matched.');
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
                "Pattern '$pattern' is invalid, character '#' is not allowed."
            );
        }
        $hasOptionalSegment = strpos($pattern, '(') !== false;
        $hasDynamicSegment = strpos($pattern, ':') !== false;
        $hasWildcardSegment = strpos($pattern, '*') !== false;
        $hasFormat = isset($options['formats']);
        if ($hasFormat === false) {
            $formats = end($this->scopeFormatStack);
            if ($formats !== false) {
                $options['formats'] = $formats;
                $hasFormat = true;
            }
        }
        if ($hasFormat && is_array($options['formats']) === false) {
            $options['formats'] = [$options['formats']];
        }
        $path = $this->getRequestPath();
        if ($hasFormat === false
            && $hasOptionalSegment === false
            && $hasWildcardSegment === false
            && $hasDynamicSegment === false
            && $this->shouldMatchScope ===  false
        ) {
            if ($pattern !== '/') {
                $pattern = '/' . $pattern;
            }
            if ($path === $pattern) {
                if (isset($options['extra'])) {
                    return $this->verifyExtraMatchConstrains($options['extra']);
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
        $isOptionalFormat = isset($options['formats']['default']);
        if ($hasFormat) {
            if ($isOptionalFormat) {
                $formatPattern = '(\.(?<format>[0-9a-zA-Z]+?))?';
            } else {
                $formatPattern = '\.(?<format>[0-9a-zA-Z]+?)';
            }
        }
        if ($this->shouldMatchScope) {
            if ($hasFormat) {
                $pattern = '#^/' . $pattern;
                if ($isOptionalFormat === false) {
                    $pattern .=  '/(.*?' . $formatPattern . ')$#';
                } else {
                    $pattern .= '($|/(.+?' . $formatPattern . '))$#';
                }
            } else {
                $pattern = '#^/' . $pattern . '($|/(.*)$)#';
            }
        } else {
            $pattern = '#^/' . $pattern . $formatPattern . '$#';
        }
        $result = preg_match($pattern, $path, $matches);
        if ($result === false) {
            throw new RoutingException("Pattern '$pattern' is invalid.");
        }
        if ($result === 1) {
            if ($hasFormat) {
                if (isset($matches['format']) === false) {
                    if (isset($options['formats']['default'])) {
                        $this->setParam(
                            'format', $options['formats']['default']
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
                $tmp = $this->verifyExtraMatchConstrains(
                    $options['extra'], $matches
                );
                if ($tmp === false) {
                    return false;
                }
            }
            if ($this->shouldMatchScope) {
                $this->scopeMatchStack[] = $matches;
                if ($hasFormat) {
                    if ($isOptionalFormat) {
                        if (isset($matches['format'])) {
                            return $matches[key($matches) - 2];
                        } else {
                            return end($matches);
                        }
                    } else {
                        return $matches[key($matches) - 1];
                    }
                }
                return end($matches);
            }
            foreach ($this->scopeMatchStack as $tmp) {
                $this->setMatches($tmp);
            }
            $this->setMatches($matches);
            $this->setMatchStatus(true);
            return true;
        }
        return false;
    }

    protected function matchScope($defination, $function) {
        if ($this->isMatched()) {
            throw new RoutingException('Previous pattern is matched.');
        }
        $path = $this->getRequestPath();
        $pattern = null;
        $options = null;
        if (is_array($defination)) {
            if (isset($defination[0]) === false) {
                throw new InvalidArgumentException("Pattern is undefeind.");
            }
            $pattern = $defination[0];
            unset($defination[0]);
            $options = $defination;
        } else {
            $pattern = $defination;
        }
        $this->shouldMatchScope = true;
        $path = $this->match($pattern, $options);
        $this->shouldMatchScope = false;
        if ($path === false) {
            return false;
        }
        $previousPath = $this->getRequestPath();
        if (isset($defination['formats'])) {
            $this->scopeFormatStack[] = $defination['formats'];
        } else {
            $this->scopeFormatStack[] = false;
        }
        $this->setRequestPath('/' . $path);
        $result = $function();
        $this->setRequestPath($previousPath);
        array_pop($this->scopeMatchStack);
        array_pop($this->scopeFormatStack);
        $this->checkResult($result);
        return $this->isMatched();
    }

    protected function getScopeMatches() {
        $result = [];
        if ($this->scopeMatchStack === null) {
            return $result;
        }
        foreach ($this->scopeMatchStack as $matches) {
            foreach ($matches as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $result;
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

    private function verifyExtraMatchConstrains($extra, array $matches = null) {
        if (is_array($extra)) {
            foreach ($extra as $function) {
                if ($function($matches) === false) {
                    return false;
                }
            }
            return true;
        } else {
            return $function($matches) !== false;
        }
    }

    protected function matchResources($pattern, array $options = null) {
        $hasOptions = $options !== null;
        $hasCollectionActions = isset($options['collection_actions']);
        $hasElementActions = isset($options['element_actions']);
        $hasDefaultActions = isset($options['actions']) === false && (
            $hasCollectionActions === false || $hasElementActions === false
        );
        if (preg_match('#[:*]id($|[/{])#', $pattern) !== 0) {
            throw new RoutingException(
                "Pattern '$pattern' is invalid. ':id' is not allowed."
            );
        }
        if ($hasOptions) {
            if (isset($options['id'])) {
                $options[':id'] = $options['id'];
            } elseif (isset($options[':id'])) {
                throw new RoutingException(
                    "Option ':id' is invalid, use 'id' instead."
                );
            } else {
                $options[':id'] = '\d+';
            }
        } else {
            $options = [':id' => '\d+'];
        }
        if ($hasOptions === false || ($hasDefaultActions
            && isset($options['default_actions']) === false
        )) {
            $options['default_actions'] = [
                'index' => ['GET', '/'],
                'show' => ['GET', ':id', 'belongs_to_element' => true],
                'new' => ['GET', 'new'],
                'edit' => ['GET', ':id/edit', 'belongs_to_element' => true],
                'create' => ['POST', '/'],
                'update' => [
                    'PATCH | PUT', ':id', 'belongs_to_element' => true
                ],
                'delete' => ['DELETE', ':id', 'belongs_to_element' => true],
            ];
            if ($hasOptions === false) {
                foreach ($options['default_actions'] as &$value) {
                    unset($value['belongs_to_element']);
                }
                return $this->matchResource($pattern, $options);
            }
        }
        if ($hasDefaultActions && $hasCollectionActions) {
            foreach ($options['default_actions'] as $key => $value) {
                if (isset($value['belongs_to_element']) === false
                    || $value['belongs_to_element'] !== true
                ) {
                    if (is_int($key)) {
                        if (is_string($value) === false) {
                            throw new RoutingException(
                                "Value of default action must be a string, "
                                    . gettype($value) . ' given.'
                            );
                        }
                        unset($options['default_actions'][$key]);
                    } else {
                        $options['default_actions'][$key]['ignore'] = true;
                    }
                }
            }
        }
        if ($hasDefaultActions && $hasElementActions) {
            foreach ($options['default_actions'] as $key => $value) {
                if (isset($value['belongs_to_element'])
                    && $value['belongs_to_element'] === true
                ) {
                    $options['default_actions'][$key]['ignore'] = true;
                }
            }
        }
        if ($hasCollectionActions && $options['collection_actions'] !== false) {
            if (isset($options['extra_actions']) === false) {
                $options['extra_actions'] = $options['collection_actions'];
            } else {
                foreach ($options['collection_actions'] as $key => $value) {
                    if (is_int($key)) {
                        $options['extra_actions'][] = $value;
                    } else {
                        if (isset($options['extra_actions'][$key])) {
                            throw new Exception;
                        }
                        $options['extra_actions'][$key] = $value;
                    }
                }
            }
        }
        $extraActions = isset($options['extra_actions']) ?
            $options['extra_actions'] : [];
        $extraElementActions = isset($options['extra_element_actions']) ?
            $options['extra_element_actions'] : null;
        $elementActions = $hasElementActions
            && $options['element_actions'] !== false ?
            $options['element_actions'] : null;
        for (;;) {
            if ($elementActions === null) {
                if ($extraElementActions !== null) {
                    $elementActions = $extraElementActions;
                    $extraElementActions = null;
                } else {
                    break;
                }
            }
            foreach ($elementActions as $key => $value) {
                if (is_int($key)) {
                    if (is_string($value) === false) {
                        throw new Exception;
                    }
                    if (isset($options['default_actions'][$value])) {
                        $default = $options['default_actions'][$value];
                        if (isset($default['belongs_to_element'])
                            && $default['belongs_to_element'] === true
                        ) {
                            $extraActions[] = $value;
                            continue;
                        }
                    }
                    $key = $value;
                    $value = [1 => ':id/' . $value];
                } else {
                    if (is_string($value)) {
                        $value = [1 => ':id/' . $value];
                    } else {
                        if (isset($value[1])) {
                            $value[1] = ':id/' . $value[1];
                        } else {
                            $value[1] = ':id/' . $key;
                        }
                    }
                }
                if (isset($extraActions[$key])) {
                    throw new Exception;
                }
                $extraActions[$key] = $value;
            }
        }
        if (count($extraActions) !== 0) {
            $options['extra_actions'] = $extraActions;
        }
        foreach ($options['default_actions'] as &$value) {
            unset($value['belongs_to_element']);
        }
        unset($options['collection_actions']);
        unset($options['element_actions']);
        unset($options['extra_element_actions']);
        return $this->matchResource($pattern, $options);
    }

    protected function matchResource($pattern, array $options = null) {
        $defaultActions = null;
        if (isset($options['default_actions'])) {
            $defaultActions = $options['default_actions'];
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
                    if (isset($actions[$value])) {
                        throw new Exception;
                    }
                    if (is_string($value) === false) {
                        throw new Exception;
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
                        if (isset($actions[$value])) {
                            throw new Exception;
                        }
                        if (is_string($value) === false) {
                            throw new Exception;
                        }
                        if (isset($defaultActions[$value])) {
                            $actions[$value] = $defaultActions[$value];
                        } else {
                            $actions[$value] = [];
                        }
                    } elseif (isset($value['ignore'])
                        && $value['ignore'] === true
                    ) {
                        unset($actions[$key]);
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
                    if (isset($actions[$value])) {
                        throw new Exception;
                    }
                    if (is_string($value) === false) {
                        throw new Exception;
                    }
                    if (isset($defaultActions[$value])) {
                        $actions[$value] = $defaultActions[$value];
                    } else {
                        $actions[$value] = [];
                    }
                }
                if (isset($actions[$key])) {
                    throw new Exception;
                }
                $action[$key] = $value;
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
                $value[0] = ['GET'];
            }
            if (in_array($requestMethod, $value[0]) === false) {
                continue;
            }
            unset($value[0]);
            $suffix = null;
            if (isset($value[1])) {
                if ($value[1] !== '/' && $value[1] != '') {
                    $suffix = '/' . $value[1];
                } else {
                    $suffix = '/';
                }
                unset($value[1]);
            } else {
                $suffix = '/' . $key;
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
                }
            } else {
                $actionOptions = $options;
            }
            $actionPattern = $pattern;
            if ($suffix !== '/') {
                if (isset($actionOptions['formats']) === null
                    && end($this->scopeFormatStack) === false
                    && preg_match('#^[^*:(#]+$#', $suffix, $matches) === 1
                ) {
                    if (substr($this->getRequestPath(), -strlen($matches[0]))
                        !== $matches[0]
                    ) {
                        continue;
                    }
                }
                $actionPattern .= $suffix;
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
                    "Empty string is a invalid return value."
                );
            }
            $segments = explode('/', $value);
            switch (count($segments)) {
                case 1:
                    $this->setAction($segments[1]);
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
                "Type '" . gettype($value) . "' of returned value is invalid."
            );
        }
        $this->setMatchStatus(true);
    }

    protected function setRequestPath($value) {
        $this->requestPath = (string)$value;
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

    protected function setAction($value) {
        $this->action = (string)$value;
    }

    protected function setControllerClass($value) {
        $this->controllerClass = (string)$value;
    }

    protected function setActionMethod($value) {
        $this->actionMethod = (string)$value;
    }

    protected function getRequestPath() {
        if ($this->requestPath === null) {
            $this->requestPath = RequestPath::get(false);
        }
        return $this->requestPath;
    }
}
