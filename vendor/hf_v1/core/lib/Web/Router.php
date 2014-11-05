<?php
namespace Hyperframework\Web;

class Router {
    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function run() {
        $result = $this->parse();
    }

    public function getResult() {
    }

    public function getPath() {
        return RequestPath::get();
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
                    if ($method === $requestMethod) {
                        $isMethodAllowed = true;
                        break;
                    }
                }
                if ($isMethodAllowed === false) {
                    return false;
                }
            }
        }
        $hasOptionalSegment = strpos($pattern, '(');
        $hasDynamicSegment = strpos($pattern, ':');
        $hasWildcardSegment = strpos($pattern, '*');
        $hasFormat = isset($options['formats']);
        if ($hasFormat && is_array($options['formats']) === false) {
            throw new Exception;
        }
        $path = $this->getPath();
        if ($hasFormat === false && $hasOptionalSegment === false && $hasDynamicSegment === false) {
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
        $result = preg_match('#^' . $pattern . '$#', $path, $matches);
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
            print_r($matches);
            return true;
        }
        return false;
    }

    protected function matchGet($pattern, $options) {
        $options['methods'] = 'get';
        return $this->match($pattern, $options);
    }

    protected function matchPost($pattern, $options) {
        $options['methods'] = 'post';
        return $this->match($pattern, $options);
    }

    protected function matchResources($pattern, $options) {
    }

    protected function matchResource($pattern, $options) {
    }

    protected function matchScope($pattern, $function, array $options = null) {
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
