<?php
namespace Hyperframework\Web;

use Hyperframework;
use Hyperframework\Config;
use Hyperframework\Web\NotFoundException;
use Exception;

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
                    $function = $options['extra'];
                    return $function() !== false;
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
            if (isset($options['extra'])) {
                $function = $options['extra'];
                if ($function() === false) {
                    return false;
                }
            }
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

    protected function matchResources($pattern, array $options = null) {
        $defaultActions = [
            'index' => ['GET'],
            'show' => ['GET', ':id', ['belongs_to_element' => true]],
            'new' => ['GET', 'new'],
            'edit' => ['GET', ':id/edit', ['belongs_to_element' => true]],
            'create' => ['POST'],
            'update' => ['PATCH | PUT', ':id', ['belongs_to_element' => true]],
            'delete' => ['DELETE', ':id', ['belongs_to_element' => true]],
        ];
    }

    protected function matchResource($pattern, array $options = null) {
        $action = null;
        $defaultActions = [
            'show' => 'GET',
            'new' => ['GET', 'new'],
            'update' => 'PATCH | PUT',
            'create' => 'POST',
            'delete' => 'DELETE',
            'edit' => ['GET', 'edit'],
        ];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        foreach ($defaultActions as $key => $value) {
            $isMethodMatched = false;
            if (is_array($value) === false) {
                $value = [$value];
            }
            if (strpos($value[0], '|') !== false) {
                $tmps = explode('|', $value[0]);
                $value[0] = [];
                foreach ($tmps as $tmp) {
                    $value[0] = strtoupper(trim($tmp));
                }
            } else {
                $value[0] = [strtoupper($value[0])];
            }
            foreach ($value[0] as $method) {
                if ($method === $requestMethod) {
                    $isMethodMatched = true;
                    break;
                }
            }
            if ($isMethodMatched === false) {
                continue;
            }
            if (isset($value[1]) !== false) {
                if ($value[1] === '' || $value[1] === '/') {
                    throw new Exception;
                }
                $suffix = '/' . $value[1];
                if (substr($this->getPath(), -strlen($suffix)) !== false) {
                    $pattern .= $suffix;
                } else {
                    continue;
                }
            }
            $matchOptions = [];
            if ($options !== null) {
                if (isset($options['id'])) {
                    $matchOptions[':id'] = $options['id'];
                }
                if (isset($options['formats'])) {
                    $matchOptions['formats'] = $options['formats'];
                }
                if (isset($options['extra'])) {
                    $matchOptions['extra'] = $options['extra'];
                }
            }
            if ($this->match($pattern, $matchOptions)) {
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
