<?php
namespace Hyperframework\Web;

class App {
    private $pathInfo;
    private $actionResult;
    private $params = array();
    private $isViewEnabled = true;

    public function run() {
        $this->initialize();
        $this->executeAction();
        $this->renderView();
    }

    protected function initialize() {
        $this->parseRequestBody();
        $this->initializePathInfo();
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
        return unset($this->params[$name]);
    }

    public function getParams() {
        return $this->params;
    }

    public function getActionResult() {
        return $this->actionResult;
    }

    public function redirect($url, $statusCode = 301) {
        header('Location: ' . $url, true, $statusCode);
        $this->isViewEnabled = false;
    }

    public function disableView() {
        $this->isViewEnabled = false;
    }

    protected function executeAction() {
        $this->actionResult = ActionDispatcher::run($this->pathInfo, $this);
    }

    protected function renderView() {
        if ($this->isViewEnabled) {
            ViewDispatcher::run($this->pathInfo, $this);
        }
    }

    protected function initailizePathInfo() {
        $this->pathInfo = PathInfo::get($this->getPath());
    }

    protected function getPath() {
        return Router::execute($this);
    }

    protected function parseRequestBody() {
        if (isset($_SERVER['CONTENT_TYPE'])
            && $_SERVER['CONTENT_TYPE'] === 'application/json'
        ) {
            JsonRequestBodyParser::run();
        }
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

    final protected function disableView() {
        $this->isViewEnabled = false;
    }

    final protected function isViewEnabled() {
        return $this->isViewEnabled;
    }
}
