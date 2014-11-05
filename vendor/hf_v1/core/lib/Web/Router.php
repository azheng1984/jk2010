<?php
namespace Hyperframework\Web;

class Router {
    private $app;
    private $path;

    public function __construct($app) {
        $this->app = $app;
    }

    public function run() {
        $result = $this->parse();
    }

    public function getResult() {
    }

    public function getPath() {
        if ($this->path === null) {
            $this->path = RequestPath::get();
            $this->path = '/' . trim($this->path, '/');
        }
        return $this->path;
    }

    protected function parse() {
    }

    protected function getParam($name) {
    }

    protected function getParams() {
    }

    protected function setParam($name, $value) {
    }

    protected function removeParam($name) {
    }

    protected function hasParam($name) {
    }

    protected function isMatched() {
    }

    protected function deleteMatch() {
    }

    protected function redirect() {
    }

    protected function getApp() {
        return $this->app;
    }

    protected function match($pattern, array $options = null) {
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
            return true;
        }
        return false;
    }

    protected function matchGet($pattern, array $options = null) {
        $options['methods'] = 'get';
        return $this->match($pattern, $options);
    }

    protected function matchPost($pattern, array $options = null) {
        $options['methods'] = 'post';
        return $this->match($pattern, $options);
    }

    //if ($this->matchResources('articles/:article_id/comments')) return;
    protected function matchResources($pattern, array $options = null) {
    }

    //if ($this->matchResource('account')) return;
    protected function matchResource($pattern, array $options = null) {
        $matchOptions = null;
        if ($options !== null) {
            if (isset($options['action'])) {
            }
            if (isset($options['formats'])) {
                $matchOptions['formats'] = $options['formats'];
            }
        }
        $action = null;
        $isMatched = false;
        $actions = ['new', 'show', 'create', 'update', 'delete', 'edit'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod === 'GET') {
             if (in_array('show', $actions)) {
                 echo $pattern . 'xx';
                 if ($this->matchGet($pattern, $matchOptions)) {
                     $action = 'show';
                     $isMatched = true;
                 }
             } elseif (in_array('new', $actions)) {
                if (substr($this->getPath(), -3) === 'new') {
                    if ($this->matchGet($pattern . '/new', $matchOptions)) {
                        $action = 'new';
                        $isMatched = true;
                    }
                }
            } elseif (in_array('edit', $actions)) {
                if (substr($this->getPath(), -4) === 'edit') {
                    if ($this->matchGet($pattern . '/edit', $matchOptions)) {
                        $action = 'edit';
                        $isMatched = true;
                    }
                }
            }
        } elseif ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $matchOptions['methods'] = ['put' , 'patch'];
            if ($this->match($pattern, $matchOptions)) {
                $action = 'update';
                $isMatched = true;
            }
        } elseif ($requestMethod === 'POST') {
            if ($this->matchPost($pattern, $matchOptions)) {
                $action = 'create';
                $isMatched = true;
            }
        } elseif ($requestMethod === 'DELETE') {
            if ($this->matchDelete($pattern, $matchOptions)) {
                $action = 'delete';
                $isMatched = true;
            }
        }
        if ($isMatched) {
            $controller = $pattern;
            if (($tmp = strrpos($pattern, '/')) !== false) {
                $controller = substr($pattern, $tmp + 1);
            }
            //todo recoverable
            $this->setController($controller);
            $this->setAction($action);
            echo '[resource ' . $controller . ' matched!]';
            if (isset($options['extra'])) {
                $function = $options['extra'];
                if ($function() !== false) {
                    return true;
                }
                return false;
            }
            return true;
        }
        return false;
    }

    protected function matchScope($prefix, $function) {
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
                //todo store result with match object
                if ($result === null) {
                }
                return true;
            }
        }
        return false;
    }

    protected function setPath($value) {
        $this->path = $value;
    }

    protected function setModule($value) {
    }

    protected function setController($value) {
    }

    protected function setAction($value) {
    }

    protected function setModuleNamespace($value) {
    }

    protected function setControllerClass($value) {
    }

    protected function setActionMethod($value) {
    }
}
