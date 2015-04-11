<?php
namespace Hyperframework\Web;

use Closure;
use LogicException;
use InvalidArgumentException;
use Hyperframework\Common\Config;
use Hyperframework\Common\NamespaceCombiner;

abstract class Router implements RouterInterface {
    private $app;
    private $params = [];
    private $module;
    private $controller;
    private $controllerClass;
    private $action;
    private $actionMethod;
    private $requestPath;
    private $isMatched = false;

    /**
     * @param AppInterface $app
     */
    public function __construct(AppInterface $app) {
        $this->app = $app;
        $result = $this->execute();
        $this->parseResult($result);
        if ($this->isMatched() === false) {
            throw new NotFoundException;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getParam($name) {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }
    }

    /**
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasParam($name) {
        return isset($this->params[$name]);
    }

    /**
     * @return string
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getController() {
        if ($this->controller === null) {
            return 'index';
        }
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getControllerClass() {
        if ($this->controllerClass !== null) {
            return $this->controllerClass;
        }
        $controller = (string)$this->getController();
        if ($controller === '') {
            return;
        }
        $tmp = ucwords(str_replace('_', ' ', $controller));
        $class = str_replace(' ', '', $tmp) . 'Controller';
        $moduleNamespace = (string)$this->getModuleNamespace();
        if ($moduleNamespace !== '' && $moduleNamespace !== '\\') {
            NamespaceCombiner::prepend($class, $moduleNamespace);
        }
        return $class;
    }

    /**
     * @return string
     */
    public function getAction() {
        if ($this->action === null) {
            return 'show';
        }
        return $this->action;
    }

    /**
     * @return string
     */
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

    /**
     * @param string $pattern
     * @param array $options
     * @return bool
     */
    protected function match($pattern, array $options = null) {
        if ($this->isMatched()) {
            throw new RoutingException('Already matched.');
        }
        if ($options !== null) {
            if (isset($options['methods'])) {
                if (is_array($options['methods']) === false) {
                    throw new RoutingException(
                        "Option 'methods' must be an array, "
                            . gettype($options['methods']) . " given."
                    );
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
        if (strpos($pattern, '?') !== false) {
            throw new RoutingException(
                "Invalid pattern '$pattern', character '?' is not allowed."
            );
        }
        $originalPattern = $pattern;
        $hasBackslash = false;
        if (strpos($pattern, '\\') !== false) {
            $hasBackslash = true;
            $pattern = str_replace(
                ['\:', '\*', '\(', '\)'], ['#0', '#1', '#2', '#3'], $pattern
            );
            $backslashPosition = strpos($pattern, '\\');
            if ($backslashPosition !== false) {
                $pattern = $originalPattern;
                if ($backslashPosition === strlen($pattern) - 1) {
                    $message = "Invalid pattern '$pattern', '\\'"
                        . " at the end of the pattern is not allowed.";
                } else {
                    $message = "Invalid pattern '$pattern', '\\'"
                        . " is not allowed before '"
                        . $pattern[$backslashPosition + 1] . "'.";
                }
                throw new RoutingException($message);
            }
        }
        $pattern = str_replace(
            ['.', '^', '$', '+', '[', '|', '{', '*'],
            ['\.', '\^', '\$', '\+', '\[', '\|', '\{', '\*'],
            $pattern
        );
        $hasOptionalSegment = strpos($pattern, '(') !== false;
        $hasDynamicSegment = strpos($pattern, ':') !== false;
        $hasWildcardSegment = strpos($pattern, '*') !== false;
        $hasFormat = isset($options['format']);
        $formats = null;
        $path = trim($this->getRequestPath(), '/');
        $pattern = trim($pattern, '/');
        if ($hasFormat) {
            $format = $options['format'];
            if ($format === false) {
                $hasFormat = false;
                throw new RoutingException(
                    "The value of option 'format' is invalid,"
                        . " 'false' is not allowed."
                );
            } elseif ($format !== true) {
                if (is_string($format)) {
                    if (preg_match('#^[0-9a-zA-Z|]+$#', $format) !== 1) {
                        $message = "The pattern '$format' of"
                            . " option 'format' is invalid";
                        if (strpos($format, ' ') !== false) {
                            $message .= ', space character is not allowed';
                        }
                        throw new RoutingException($message . '.');
                    }
                    $formats = explode('|', $format);
                } else {
                    throw new RoutingException(
                        "Option 'format' type '"
                            . gettype($options['format']) . "' is invalid."
                    );
                }
            }
        }
        if ($hasFormat === false
            && $hasOptionalSegment === false
            && $hasWildcardSegment === false
            && $hasDynamicSegment === false
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
            $length = strlen($pattern);
            $count = 0;
            for ($index = 0; $index < $length; ++$index) {
                if ($pattern[$index] === '(') {
                    ++$count;
                }
                if ($pattern[$index] === ')') {
                    --$count;
                    if ($count < 0) {
                        break;
                    }
                }
            }
            if ($count !== 0) {
                $source = '(';
                if ($count < 0) {
                    $srouce = ')';
                }
                throw new RoutingException("Invalid pattern '$originalPattern',"
                    . " '$source' is not closed.");
            }
            $pattern = str_replace(')', ')?', $pattern);
        }
        $namedSegments = [];
        if ($hasFormat) {
            $namedSegments[] = 'format';
        }
        $namedSegmentPattern = '[^/]+';
        $duplicatedNamedSegment = null;
        $callback = function($matches) use (
            &$namedSegments,
            &$duplicatedNamedSegment,
            &$namedSegmentPattern
        ) {
            $segment = $matches[1];
            if (isset($namedSegments[$segment])
                && $duplicatedNamedSegment === null
            ) {
                $duplicatedNamedSegment = $segment;
            } else {
                $namedSegments[$segment] = true;
            }
            return "(?<$segment>$namedSegmentPattern?)";
        };
        if ($hasDynamicSegment) {
            $pattern = preg_replace_callback(
                '#\\\\\{:([a-zA-Z_][a-zA-Z0-9_]*)}#', $callback, $pattern
            );
            $pattern = preg_replace_callback(
                '#:([a-zA-Z_][a-zA-Z0-9_]*)#', $callback, $pattern
            );
        }
        if ($hasWildcardSegment) {
            $namedSegmentPattern = '.+';
            $pattern = preg_replace_callback(
                '#\\\\\{\\\\\*([a-zA-Z_][a-zA-Z0-9_]*)}#', $callback, $pattern
            );
            $pattern = preg_replace_callback(
                '#\\\\\*([a-zA-Z_][a-zA-Z0-9_]*)#', $callback, $pattern
            );
        }
        if ($duplicatedNamedSegment !== null) {
            throw new RoutingException(
                "Invalid pattern '$originalPattern', "
                    . "named segment '$duplicatedNamedSegment' is duplicated."
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
        if ($hasBackslash) {
            $pattern = str_replace(
                ['#0', '#1', '#2', '#3'], ['\:', '\*', '\(', '\)'], $pattern
            );
        }
        $pattern = '#^' . $pattern . $formatPattern . '$#';
        if (isset($GLOBALS['show'])) {
            echo $pattern;
        }
        $result = preg_match($pattern, $path, $matches);
        if ($result === 1) {
            if ($options !== null) {
                foreach ($options as $key => $value) {
                    if (is_string($key) && $key !== '' && $key[0] === ':') {
                        if ($hasFormat && $key === ':format') {
                            throw new RoutingException(
                                "Dynamic segment ':format' is reserved, use "
                                    . "option 'format' to change"
                                    . " the rule of format."
                            );
                        }
                        $name = substr($key, 1);
                        if (isset($matches[$name]) === false) {
                            continue;
                        }
                        $segment = $matches[$name];
                        if (strpos($value, '#') !== false) {
                            throw new RoutingException(
                                "Invalid pattern '$value', character '#' is not"
                                    . " allowed, defined in option '$key'."
                            );
                        }
                        $result = preg_match('#^' . $value . '$#', $segment);
                        if ($result === false) {
                            throw new RoutingException(
                                "Invalid pattern '$value', defined in option '"
                                    . "$key'."
                            );
                        }
                        if ($result !== 1) {
                            return false;
                        }
                    }
                }
            }
            if ($hasFormat) {
                if (isset($matches['format']) === false) {
                    if (isset($options['default_format'])) {
                        $this->setParam(
                            'format', $options['default_format']
                        );
                    } else {
                        return false;
                    }
                } elseif ($formats !== null
                    && in_array($matches['format'], $formats) === false
                ) {
                    return false;
                }
            }
            $pattern = '#^[a-zA-Z][a-zA-Z0-9_]*$#';
            if (isset($matches['module'])
                && isset($options[':module']) === false
            ) {
                $segments = explode('/', $matches['module']);
                foreach ($segments as $segment) {
                    if (preg_match($pattern, $segment) === 0) {
                        return false;
                    }
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
            $this->setMatches($matches);
            $this->setMatchStatus(true);
            return true;
        }
        return false;
    }

    /**
     * @param string $path
     * @param Closure $callback
     * @return bool
     */
    protected function matchScope($path, Closure $callback) {
        if ($this->isMatched()) {
            throw new RoutingException('Already matched.');
        }
        $path = trim($path, '/');
        $orignalPath = $this->getRequestPath();
        $requestPath = trim($orignalPath, '/');
        $currentPath = '/';
        if ($path !== '') {
            $pathLength = strlen($path);
            if (strncmp($path, $requestPath, $pathLength) === 0) {
                $previousPathLength = strlen($requestPath);
                if ($previousPathLength !== $pathLength) {
                    if ($requestPath[$pathLength] !== '/') {
                        return false;
                    }
                    $currentPath = substr($requestPath, $pathLength);
                }
            } else {
                return false;
            }
        }
        $this->setRequestPath($currentPath);
        $result = $callback();
        $this->parseResult($result);
        $this->setRequestPath($orignalPath);
        return $this->isMatched();
    }

    /**
     * @param string $pattern
     * @param array $options
     * @return bool
     */
    protected function matchResource($pattern, array $options = null) {
        if (is_string($pattern) === false) {
            throw new InvalidArgumentException(
                "Argument 'pattern' must be a string, "
                    . gettype($pattern) . ' given.'
            );
        }
        if ($options !== null) {
            $actionOptions = [
                'actions',
                'default_actions'
            ];
            foreach ($actionOptions as $actionOption) {
                if (isset($options[$actionOption])
                    && is_array($options[$actionOption]) === false
                ) {
                    throw new RoutingException(
                        "Option '$actionOption' must be an array, "
                            . gettype($options[$actionOption]) . ' given.'
                    );
                }
            }
        }
        $defaultActions = null;
        if (isset($options['default_actions'])) {
            $defaultActions = $options['default_actions'];
            unset($options['default_actions']);
        } else {
            $defaultActions = [
                'show' => ['GET', '/'],
                'new',
                'update' => [['PATCH', 'PUT'], '/'],
                'create' => ['POST', '/'],
                'delete' => ['DELETE', '/'],
                'edit'
            ];
        }
        if (isset($options['actions'])) {
            $actions = $options['actions'];
            if ($options['actions'] !== false) {
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
            } else {
                $actions = [];
            }
            unset($options['actions']);
        } else {
            $actions = $defaultActions;
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
        }
        if (count($actions) === 0) {
            return false;
        }
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $pattern = rtrim($pattern, '/');
        $action = null;
        foreach ($actions as $action => $value) {
            if (is_array($value) === false) {
                $value = [$value];
            }
            if (isset($value[0])) {
                if (is_string($value[0])) {
                    $value['methods'] = [$value[0]];
                } elseif (is_array($value[0]) === false) {
                    throw new RoutingException(
                        "Allowed request methods of action '$action'"
                            . " must be a string or an array, "
                            . gettype($value[0]) . ' given.'
                    );
                } else {
                    $value['methods'] = $value[0];
                }
            } else {
                $value['methods'] = ['GET'];
            }
            unset($value[0]);
            if (isset($value[1])) {
                if (is_string($value[1]) === false) {
                    throw new RoutingException(
                        "Path of action '$action' must be a string, "
                            . gettype($value[1]) . ' given.'
                    );
                }
                $suffix = $value[1];
                unset($value[1]);
            } else {
                $suffix = $action;
            }
            if (count($value) !== 0) {
                $actionOptions = $value;
                $actionExtra = null;
                if (isset($actionOptions['extra'])) {
                    $actionExtra = $actionOptions['extra'];
                }
                if ($options !== null) {
                    $actionOptions = $actionOptions + $options;
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

    /**
     * @param string $pattern
     * @param array $options
     * @return bool
     */
    protected function matchResources($pattern, array $options = null) {
        if (is_string($pattern) === false) {
            throw new InvalidArgumentException(
                "Argument 'pattern' must be a string, "
                    . gettype($pattern) . ' given.'
            );
        }
        if (preg_match('#[:*]id($|[/{])#', $pattern) !== 0) {
            throw new RoutingException(
                "Invalid pattern '$pattern', "
                    . "dynamic segment ':id' is reserved."
            );
        }
        $hasOptions = $options !== null;
        if ($hasOptions) {
            if (isset($options[':id'])) {
                throw new RoutingException(
                    "Invalid option ':id', "
                        . "use option 'id' to change the pattern of element id."
                );
            }
            if (isset($options['id'])) {
                $options[':id'] = $options['id'];
            } else {
                $options[':id'] = '\d+';
            }
        } else {
            $options = [':id' => '\d+'];
        }
        if ($hasOptions) {
            $actionOptions = [
                'default_actions', 'element_acitons', 'collection_actions'
            ];
            foreach ($actionOptions as $actionOption) {
                if (isset($options[$actionOption])
                    && is_array($options[$actionOption]) === false
                ) {
                    throw new RoutingException(
                        "Option '$actionOption' must be an array, "
                            . gettype($options[$actionOption]) . ' given.'
                    );
                }
            }
        }
        if ($hasOptions === false
            || isset($options['default_actions']) === false
        ) {
            $defaultActions = [
                'index' => ['GET', '/'],
                'show' => ['GET', '/', 'belongs_to_element' => true],
                'new' => [],
                'edit' => ['belongs_to_element' => true],
                'create' => ['POST', '/'],
                'update' => [
                    ['PATCH', 'PUT'], '/', 'belongs_to_element' => true
                ],
                'delete' => ['DELETE', '/', 'belongs_to_element' => true],
            ];
        } else {
            $defaultActions = [];
            foreach ($options['default_actions'] as $key => $value) {
                if (is_int($key)) {
                    if (is_string($value) === false) {
                        throw new RoutingException(
                            'Action name must be a string, '
                                . gettype($value) . ' given.'
                        );
                    }
                    $defaultActions[$value] = [];
                } else {
                    $defaultActions[$key] = $value;
                }
            }
        }
        if (isset($options['collection_actions'])) {
            foreach ($options['collection_actions'] as $key => $value) {
                if (is_int($key)) {
                    if (isset($defaultActions[$value])) {
                        $action = $defaultActions[$value];
                        if (isset($action['belongs_to_element']) === true
                            && $action['belongs_to_element'] === true
                        ) {
                            unset($options['collection_actions'][$key]);
                            $options['collection_actions'][$value] = [];
                        }
                    }
                }
            }
            $options['actions'] = $options['collection_actions'];
        } else {
            $options['actions'] = [];
            foreach ($defaultActions as $key => $value) {
                if (isset($value['belongs_to_element']) === false
                    || $value['belongs_to_element'] !== true
                ) {
                    $actionName = $value;
                    if (is_int($key)) {
                        $options['actions'][] = $value;
                    } else {
                        $options['actions'][] = $key;
                    }
                }
            }
        }
        if (isset($options['element_actions'])) {
            $actions = $this->convertElementActionsToCollectionActions(
                $options['element_actions'], $defaultActions
            );
            $options['actions'] = array_merge(
                $options['actions'], $actions
            );
        } else {
            foreach ($defaultActions as $key => $value) {
                if (isset($value['belongs_to_element'])
                    && $value['belongs_to_element'] === true
                ) {
                    if (is_int($key)) {
                        $options['actions'][] = $value;
                    } else {
                        $options['actions'][] = $key;
                    }
                }
            }
        }
        $options['default_actions'] =
            $this->convertElementActionsToCollectionActions(
                $defaultActions, null, true
            );
        return $this->matchResource($pattern, $options);
    }

    /**
     * @param string $pattern
     * @param array $options
     * @return bool
     */
    protected function matchGet($pattern, array $options = null) {
        $options['methods'] = ['GET'];
        return $this->match($pattern, $options);
    }

    /**
     * @param string $pattern
     * @param array $options
     * @return bool
     */
    protected function matchPost($pattern, array $options = null) {
        $options['methods'] = ['POST'];
        return $this->match($pattern, $options);
    }

    /**
     * @param string $pattern
     * @param array $options
     * @return bool
     */
    protected function matchPut($pattern, array $options = null) {
        $options['methods'] = ['PUT'];
        return $this->match($pattern, $options);
    }

    /**
     * @param string $pattern
     * @param array $options
     * @return bool
     */
    protected function matchPatch($pattern, array $options = null) {
        $options['methods'] = ['PATCH'];
        return $this->match($pattern, $options);
    }

    /**
     * @param string $pattern
     * @param array $options
     * @return bool
     */
    protected function matchDelete($pattern, array $options = null) {
        $options['methods'] = ['DELETE'];
        return $this->match($pattern, $options);
    }

    /**
     * @param string $url
     * @param int $statusCode
     */
    protected function redirect($url, $statusCode = 302) {
        Response::setHeader('Location: ' . $url, true, $statusCode);
        $this->getApp()->quit();
    }

    /**
     * @return bool
     */
    protected function isMatched() {
        return $this->isMatched;
    }

    /**
     * @param bool $isMatched
     */
    protected function setMatchStatus($isMatched) {
        $this->isMatched = $isMatched;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    protected function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    /**
     * @param string $name
     */
    protected function removeParam($name) {
        unset($this->params[$name]);
    }

    /**
     * @param string $module
     */
    protected function setModule($module) {
        $this->module = (string)$module;
    }

    /**
     * @param string $controller
     */
    protected function setController($controller) {
        $this->controller = (string)$controller;
    }

    /**
     * @param string $controllerClass
     */
    protected function setControllerClass($controllerClass) {
        $this->controllerClass = (string)$controllerClass;
    }

    /**
     * @param string $actionMethod
     */
    protected function setAction($action) {
        $this->action = (string)$action;
    }

    /**
     * @param string $actionMethod
     */
    protected function setActionMethod($actionMethod) {
        $this->actionMethod = (string)$actionMethod;
    }

    /**
     * @return string
     */
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

    /**
     * @return AppInterface
     */
    protected function getApp() {
        if ($this->app === null) {
            throw new LogicException(
                "App cannot be null, constructor method of class"
                    . " '" . __CLASS__ . "' is not called."
            );
        }
        return $this->app;
    }

    /**
     * @return string
     */
    private function getModuleNamespace() {
        $rootNamespace = 'Controllers';
        $appRootNamespace = Config::getAppRootNamespace();
        if ($appRootNamespace !== '' && $appRootNamespace !== '\\') {
            NamespaceCombiner::prepend($rootNamespace, $appRootNamespace);
        }
        $module = (string)$this->getModule();
        if ($module === '') {
            return $rootNamespace;
        }
        $tmp = str_replace(
            ' ', '\\', ucwords(str_replace('/', ' ', $module))
        );
        $namespace = str_replace(' ', '', ucwords(str_replace('_', ' ', $tmp)));
        NamespaceCombiner::prepend($namespace, $rootNamespace);
        return $namespace;
    }

    /**
     * @param array $actions
     * @param array $defaultActions
     * @param bool $isMixed
     * @return array
     */
    private function convertElementActionsToCollectionActions(
        array $actions, array $defaultActions = null, $isMixed = false
    ) {
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
            if (is_array($value)) {
                if (isset($value[1])) {
                    if (is_string($value[1]) === false) {
                        throw new RoutingException(
                            "Path of action '$key' must be a string, "
                            . gettype($value[1]) . ' given.'
                        );
                    }
                    $path = $value[1];
                } else {
                    if (isset($value[0]) === false) {
                        $value[0] = 'GET';
                    }
                    $path = $key;
                }
                $path = ltrim($path, '/');
                if ($path !== '') {
                    $value[1] = ':id/' . $path;
                } else {
                    $value[1] = ':id';
                }
            } else {
                $value = [$value, ':id'];
            }
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * @param mixed $extra
     * @param array $matches
     * @return bool
     */
    private function verifyExtraRules($extra, array $matches = []) {
        foreach ($matches as $key => $value) {
            if (is_int($key)) {
                unset($matches[$key]);
            }
        }
        if (is_array($extra)) {
            foreach ($extra as $function) {
                if ($function instanceof Closure === false) {
                    $type = gettype($function);
                    if ($type === 'Object') {
                        $type = get_class($function);
                    }
                    throw new RoutingException(
                        'Extra rule must be a closure, ' . $type . ' given.'
                    );
                }
                $result = (bool)$function($matches);
                if ($result !== true) {
                    return false;
                }
            }
            return true;
        } else {
            if ($extra instanceof Closure === false) {
                $type = gettype($extra);
                if ($type === 'Object') {
                    $type = get_class($extra);
                }
                throw new RoutingException(
                    'Extra rule must be a closure, ' . $type . ' given.'
                );
            }
            return (bool)$extra($matches);
        }
    }

    /**
     * @param array $matches
     */
    private function setMatches(array $matches) {
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

    /**
     * @param mixed $value
     */
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

    /**
     * @param string $requestPath
     */
    private function setRequestPath($requestPath) {
        $this->requestPath = (string)$requestPath;
    }
}
