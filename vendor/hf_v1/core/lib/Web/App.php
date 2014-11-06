<?php
namespace Hyperframework\Web;

use Exception;

class App {
    private $router;
    private $actionResult;
    private $view;
    private $isViewEnabled;

    public function run() {
        $this->initialize();
        $this->executeAction();
        $this->renderView();
        $this->finalize();
    }

    public function getRouter() {
        return $this->router;
    }

    protected function setRouter($router) {
        $this->router = $router;
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
        $this->initializeRouter();
        $this->initializePathInfo();
    }

    protected function executeAction() {
        $this->actionResult = ControllerHandler::handle($this->pathInfo, $this);
    }

    protected function renderView() {
        ViewDispatcher::dispatch($this->pathInfo, $this);
    }

    protected function finalize() {}

    //todo remove
    protected function rewriteRequestMethod() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['_method']) &&
            Config::get('hyperframework.rewrite_request_method') !== false
        ) {
            $_SERVER['ORIGINAL_REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
            $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
        }
    }

    //todo remove postpone
    protected function parseRequestBody() {
        if (isset($_SERVER['CONTENT_TYPE'])
            && $_SERVER['CONTENT_TYPE'] === 'application/json'
        ) {
            JsonRequestBodyParser::parse();
        }
    }

    protected function initializeRouter() {
        $this->router = new Router($this);
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
