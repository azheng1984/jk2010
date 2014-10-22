<?php

class Context {
    private $ctx = $ctx;

    public function __construct($ctx) {
        $ctx = $ctx;
    }

    public function get($ctx) {
        $ctx->quit();
        $this->redirect();
    }

    protected static function getParam($name) {
    }

    protected function hasParam() {
    }

    protected function setParam() {
    }

    protected function quit() {
    }

    protected function redirect() {
    }

    protected function getContext() {
    }
}
