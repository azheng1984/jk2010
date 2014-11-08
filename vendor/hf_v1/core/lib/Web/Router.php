<?php
namespace Hyperframework\Web;

use Exception;
use Hyperframework;
use Hyperframework\Config;
use Hyperframework\Web\NotFoundException;

abstract class Router {
    private $app;
    private $params = [];
    private $module;
    private $moduleNamespace;
    private $controller;
    private $controllerClass;
    private $action;
    private $actionMethod;
    private $path;
    private $isMatched;

    public function __construct($app) {
        $this->app = $app;
        $this->parseReturnValue($this->parse());
        if ($this->isMatched() === false) {
            throw new NotFoundException;
        }
    }

    protected function parse() {
        if ($this->match('/')) return;
        $pattern = ':controller/:action';
        if (Config::get('hyperframework.web.enable_module') === true) {
            $pattern = '(:module/)' . $pattern;
        }
        $this->match($pattern);
    }

    protected function getPath() {
        if ($this->path === null) {
            $this->path = RequestPath::get();
            $this->path = '/' . trim($this->path, '/');
        }
        return $this->path;
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

    public function getModuleNamespace() {
        if (Config::get('hyperframework.web.enable_module') !== true) {
            return;
        }
        if ($this->moduleNamespace !== null) {
            return $this->moduleNamespace;
        }
        if ($this->module === null) {
            return 'Main';
        }
        return str_replace(
            ' ', '', ucwords(str_replace('_', ' ', $this->module))
        );
    }

    public function getControllerClass() {
        $class = null;
        if ($this->controllerClass !== null) {
            $class = $this->controllerClass;
        } else {
            $controller = $this->getController();
            if ($controller === null) {
                $class = 'IndexController';
            } else {
                $tmp = ucwords(str_replace('_', ' ', $controller));
                $class = str_replace(' ', '', $tmp) . 'Controller';
            }
        }
        if ($class[0] === '\\') {
            return substr($class, 1);
        }
        $moduleNamespace = null;
        $namespace = Hyperframework\APP_ROOT_NAMESPACE;
        if ($namespace !== null) {
            $namespace .= '\\';
        }
        $moduleNamespace = $this->getModuleNamespace();
        if ($moduleNamespace !== null) {
            $namespace .= $moduleNamespace . '\\';
        }
        return $namespace . 'Controllers\\' . $class;
    }

    public function getActionMethod() {
        if ($this->actionMethod !== null) {
            return $this->actionMethod;
        }
        if ($this->action === null) {
            return 'doShowAction';
        }
        $tmp = str_replace(
            ' ', '', ucwords(str_replace('_', ' ', $this->action))
        );
        return 'do' . $tmp . 'Action';
    }

    protected function isMatched() {
        return $this->isMatched;
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
            throw new Exception;
        }
        if ($options !== null) {
            if (isset($options['methods'])) {
                if (is_string($options['methods'])) {
                    $options['methods'] = [$options['methods']];
                }
                $requestMethod = $_SERVER['REQUEST_METHOD'];
                $isMethodAllowed = false;
                foreach ($options['methods'] as $method) {
                    if (strtoupper($method) === $requestMethod) {
                        $isMethodAllowed = true;
                        break;
                    }
                }
                if ($isMethodAllowed === false) {
                    return false;
                }
            }
        }
        $hasOptionalSegment = strpos($pattern, '(') !== false;
        $hasDynamicSegment = strpos($pattern, ':') !== false;
        $hasWildcardSegment = strpos($pattern, '*') !== false;
        $hasFormat = isset($options['formats']);
        if ($hasFormat && is_array($options['formats']) === false) {
            $options['formats'] = [$options['formats']];
        }
        $path = $this->getPath();
        if ($hasFormat === false
            && $hasOptionalSegment === false
            && $hasWildcardSegment === false
            && $hasDynamicSegment === false
        ) {
            if ($pattern !== '/') {
                $pattern = '/' . $pattern;
            }
            if ($path === $pattern) {
                if (isset($options['extra'])) {
                    return $this->verifyExtraMatchConstrains($options['extra']);
                }
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
                    '#\{?\#:([a-zA-Z0-9]+)\}?#',
                    '(?<\1>[^/]+?)',
                    $pattern
                );
            }
        }
        if ($hasWildcardSegment) {
            $pattern = preg_replace(
                '#\{?\*([a-zA-Z0-9]+)\}?#',
                '(?<\1>.+?)',
                $pattern
            );
        }
        if ($hasFormat) {
            if (isset($options['formats']['default'])) {
                $pattern .= '(\.(?<format>[0-9a-zA-Z.]+?))?';
            } else {
                $pattern .= '\.(?<format>[0-9a-zA-Z.]+?)';
            }
        }
        echo $pattern;
        $result = preg_match('#^/' . $pattern . '$#', $path, $matches);
        if ($result === false) {
            throw new Exception;
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
            print_r($matches);
            if (isset($matches['module']) && isset($options[':module']) === false) {
                if (preg_match($pattern, $matches['module']) === 0) {
                    return false;
                }
            }
            if (isset($matches['controller']) && isset($options[':controller']) === false) {
                if (preg_match($pattern, $matches['controller']) === 0) {
                    return false;
                }
            }
            if (isset($matches['action']) && isset($options[':action']) === false) {
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
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    if ($key === 'module') {
                        $pattern = '#^[a-zA-Z_][a-zA-Z0-9_]*$#';
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
            $this->setMatchStatus(true);
            return true;
        }
        return false;
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
        $hasOptions = true;
        if ($options === null) {
            $hasOptions = false;
            $options = [];
        }
        if ($hasOptions === false) {
            return $this->matchResource($pattern, $options);
        }
        $hasDefaultActions = isset($options['actions']) === false;
        if ($hasDefaultActions
            && isset($options['default_actions']) === false
        ) {
            $options['default_actions'] = [
                'index' => ['GET', '/'],
                'show' => ['GET', ':id', 'belongs_to_element' => true],
                'new' => ['GET', 'new'],
                'edit' => ['GET', ':id/edit', 'belongs_to_element' => true],
                'create' => ['POST', '/'],
                'update' => ['PATCH | PUT', ':id', 'belongs_to_element' => true],
                'delete' => ['DELETE', ':id', 'belongs_to_element' => true],
            ];
        }
        if ($hasDefaultActions
            && isset($options['ignore_collection_actions'])
        ) {
            foreach ($options['default_actions'] as $key => $value) {
                if (isset($value['belongs_to_element']) === false
                    || $value['belongs_to_element'] !== true
                ) {
                    if (is_int($key)) {
                        unset($options['default_actions'][$key]);
                    } else {
                        $options['default_actions'][$key]['ignore'] = true;
                    }
                }
            }
        }
        if ($hasDefaultActions
            && (isset($options['ignore_element_actions'])
                || isset($options['element_actions'])
            )
        ) {
            foreach ($options['default_actions'] as $key => $value) {
                if (isset($value['belongs_to_element'])
                    && $value['belongs_to_element'] === true
                ) {
                    $options['default_actions'][$key]['ignore'] = true;
                }
            }
        }
        $tmp = null;
        $extraAction = isset($options['extra_actions']) ?
            $options['extra_actions'] : null;
        $extraElementActions = isset($options['extra_element_actions']) ?
            $options['extra_element_actions'] : null;
        $elementActions = isset($options['element_actions']) ?
            $options['element_actions'] : null;
        for (;;) {
            if ($elementActions === null) {
                if ($extraAction !== null) {
                    if ($tmp === null) {
                        $tmp = $extraAction;
                    } else {
                        array_merge($tmp, $extraAction);
                    }
                }
                if ($extraElementActions !== null) {
                    $elementActions = $extraElementActions;
                    $extraElementActions = null;
                } else {
                    break;
                }
            }
            if ($tmp === null) {
                $tmp = [];
            }
            foreach ($elementActions as $key => $value) {
                if (is_int($key)) {
                    if (isset($options['default_actions'][$value])) {
                        $tmp[$value] = $options['default_actions'][$value];
                        continue;
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
                $tmp[$key] = $value;
            }
        }
        if ($tmp !== null) {
            $options['extra_actions'] = $tmp;
        }
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
                    if (isset($defaultActions[$value])) {
                        $actions[$value] = $defaultActions[$value];
                    } else {
                        $actions[$value] = [];
                    }
                }
            }
        } elseif (isset($options['ignored_actions'])) {
            if ($defaultActions !== null) {
                $actions = $defaultActions;
                foreach ($actions as $key => $value) {
                    if (is_int($key)) {
                        unset($actions[$key]);
                        if (isset($actions[$value])) {
                            throw new Exception;
                        }
                        $actions[$value] = [];
                    } elseif (isset($value['ignore'])
                        && $value['ignore'] === true
                    ) {
                        unset($actions[$key]);
                    }
                }
                foreach ($options['ignored_actions'] as $action) {
                    unset($defaultActions[$action]);
                }
            }
        }
        if (isset($options['extra_actions'])) {
            foreach ($options['extra_actions'] as $key => $value) {
                if (is_int($key)) {
                    unset($options['extra_actions']);
                    if (isset($options['extra_actions'][$value])) {
                        throw new Exception;
                    }
                    $options['extra_actions'][$value] = [];
                }
            }
            if ($actions === null) {
                $actions = $options['extra_actions'];
            } else {
                $actions = array_merge($actions, $options['extra_actions']);
            }
        }
        if ($actions === null || count($actions) === 0) {
            throw new Exception;
        }
        if (isset($options['id'])) {
            $options[':id'] = $options['id'];
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
                        $value[0] = strtoupper(trim($tmp));
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
                $suffix = '/' . $value[1];
                unset($value[1]);
            } else {
                $suffix = '/' . $key;
            }
            $actionPattern = $pattern;
            if ($suffix !== '/') {
                if (preg_match('#[0-9a-zA-Z_]#', $suffix) === 1) {
                    if (substr($this->getPath(), -strlen($suffix)) === false) {
                        continue;
                    }
                }
                $actionPattern .= $suffix;
            }
            $actionOptions = null;
            if (count($value) !== 0) {
                $actionOptions = $value;
                $actionExtra = null;
                if (isset($actionOptions['extra'])) {
                    $actionExtra = $actionOptions['extra'];
                }
                if ($options !== null) {
                    $actionOptoins = $options + $actionOptions;
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
            if ($this->match($actionPattern, $actionOptions)) {
                $action = $key;
            }
            break;
        }
        if ($this->isMatched()) {
            $controller = $pattern;
            if (($slashPosition = strrpos($pattern, '/')) !== false) {
                $controller = substr($pattern, $slashPosition + 1);
            }
            $this->setController($controller);
            $this->setAction($action);
            echo '[resource action ' . $controller .'/' . $action . ' matched!]';
            return true;
        }
        return false;
    }

    protected function matchScope($prefix, $function) {
        if ($this->isMatched()) {
            throw new Exception;
        }
        $path = $this->getPath();
        $prefix = '/' . $prefix;
        if (strncmp($path, $prefix, strlen($prefix)) === 0) {
            $tmp = substr($path, strlen($prefix));
            if ($tmp === '') {
                $tmp = '/';
            }
            if ($tmp[0] !== '/') {
                return false;
            }
            $this->setPath($tmp);
            $result = $function();
            $this->setPath($path);
            if ($this->isMatched()) {
                $this->parseReturnValue($result);
                return $result !== false;
            }
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

    private function parseReturnValue($value) {
        if ($value === null) {
            return;
        }
        if ($value === false) {
            $this->setMatchStatus(false);
            return;
        }
        if (is_string($result)) {
            $tmps = explode('/', $result);
            switch (count($tmps)) {
                case 1:
                    $this->setAction($tmps[0]);
                    break;
                case 2:
                    $this->setController($tmps[0]);
                    $this->setAction($tmps[1]);
                case 3:
                    $this->setModule($tmps[0]);
                    $this->setController($tmps[1]);
                    $this->setAction($tmps[2]);
                default:
                    throw new Exception;
            }
        } elseif ($value !== true) {
            throw new Exception;
        }
        $this->setMatchStatus(true);
    }

    protected function setPath($value) {
        $this->path = $value;
    }

    protected function setModule($value) {
        $this->module = $value;
    }

    public function getModule($param) {
        return $this->module;
    }

    protected function setModuleNamespace($value) {
        $this->moduleNamespace = $value;
    }

    protected function setController($value) {
        $this->controller = $value;
    }

    protected function setAction($value) {
        $this->action = $value;
    }

    protected function setControllerClass($value) {
        $this->controllerClass = $value;
    }

    protected function setActionMethod($value) {
        $this->actionMethod = $value;
    }
}
