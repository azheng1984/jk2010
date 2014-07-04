<?php
namespace Hyperframework\Web;

class App {
    private $pathInfo;
    private $actionResult;
    private $params = array();

    public function run() {
        $this->initialize();
        $this->executeAction();
        $this->renderView();
        $this->finalize();
    }

    public function getParam($name) {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }
    }

    public function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    public function hasParam($name) {
        return isset($this->params[$name]);
    }

    public function removeParam($name) {
        unset($this->params[$name]);
    }

    public function getParams() {
        return $this->params;
    }

    public function filter($fields, $source = null) {
        return InputFilter::run($fields, $source);
    }

    public function getForm($name) {
        return FormFilter::run($name);
    }

    public function getActionResult($name = null) {
        if ($name === null) {
            return $this->actionResult;
        }
        if (isset($this->actionResult[$name])) {
            return $this->actionResult[$name];
        }
    }

    public function redirect($url, $statusCode = 302) {
        header('Location: ' . $url, true, $statusCode);
        $this->quit();
    }

    public function quit() {
        $this->finalize();
        exit;
    }

    protected function initialize() {
        $this->rewriteRequestMethod();
        //todo csrf check
        $this->parseRequestBody();
        $this->initializePathInfo();
    }

    protected function executeAction() {
        $this->actionResult = ActionDispatcher::run($this->pathInfo, $this);
    }

    protected function renderView() {
        ViewDispatcher::run($this->pathInfo, $this);
    }

    protected function finalize() {}

    protected function rewriteRequestMethod() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['_method']) &&
            Config::get('hyperframework.web.rewrite_request_method') !== true 
        ) {
            $_SERVER['ORIGINAL_REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
            $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
        }
    }

    protected function parseRequestBody() {
        if (isset($_SERVER['CONTENT_TYPE'])
            && $_SERVER['CONTENT_TYPE'] === 'application/json'
        ) {
            JsonRequestBodyParser::run();
        }
    }

    protected function initializePathInfo() {
        $this->pathInfo = PathInfo::get($this->getPath());
    }

    protected function getPath() {
        return Router::run($this);
    }

    final protected function setActionResult($value) {
        return $this->actionResult = $value;
    }

    final protected function getPathInfo() {
        return $this->pathInfo;
    }

    final protected function setPathInfo($value) {
        $this->pathInfo = $value;
    }
}
