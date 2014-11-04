<?php
namespace Hyperframework\Web;

use Exception;

class App {
    private $path;
    private $pathInfo;
    private $view;
    private $routeParams = array();
    private $actionResult;

    public function run() {
        $this->initialize();
        $this->executeAction();
        $this->renderView();
        $this->finalize();
    }

    public function getRouteParam($name) {
        return $this->routeParams[$name];
    }

    public function getRouteParams() {
        return $this->routeParams;
    }

    public function setRouteParam($name, $value) {
        $this->routeParams[$name] = $value;
    }

    public function removeRouteParam($name) {
        unset($this->routeParams[$name]);
    }

    public function hasRouteParam($name) {
        return isset($this->routeParams[$name]);
    }

    public function getActionResult($name = null) {
        if ($name === null) {
            $result = $this->actionResult;
        }
        return $this->actionResult[$name];
    }

    public function redirect($url, $statusCode = 302) {
        header('Location: ' . $url, true, $statusCode);
        $this->quit();
    }

    public function disableView() {
    }

    public function enableView() {
    }

    public function setView($value) {
        $this->view = $value;
    }

    public function quit() {
        $this->finalize();
        exit;
    }

    protected function initialize() {
        $this->rewriteRequestMethod();
        $this->parseRequestBody();
        $this->initializePath();
        $this->initializePathInfo();
    }

    protected function executeAction() {
        $this->actionResult = ControllerHandler::handle($this->pathInfo, $this);
    }

    protected function renderView() {
        ViewDispatcher::dispatch($this->pathInfo, $this);
    }

    protected function finalize() {}

    protected function rewriteRequestMethod() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['_method']) &&
            Config::get('hyperframework.rewrite_request_method') !== false
        ) {
            $_SERVER['ORIGINAL_REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
            $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
        }
    }

    protected function parseRequestBody() {
        if (isset($_SERVER['CONTENT_TYPE'])
            && $_SERVER['CONTENT_TYPE'] === 'application/json'
        ) {
            JsonRequestBodyParser::parse();
        }
    }

    protected function initializePath() {
        $this->path = Router::execute($this);
    }

    protected function initializePathInfo() {
        $this->pathInfo = PathInfo::get($this->path);
    }

    final protected function getPath() {
        return $this->path;
    }

    final protected function setPath($value) {
        return $this->path = $value;
    }

    final protected function getPathInfo() {
        return $this->pathInfo;
    }

    final protected function setPathInfo($value) {
        $this->pathInfo = $value;
    }

    final protected function setActionResult($value) {
        return $this->actionResult = $value;
    }
}
