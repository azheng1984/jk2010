<?php
namespace Hyperframework\Cli;

abstract class Executor {
    private $ctx;

    public function __construct($ctx) {
        $this->ctx = $ctx;
    }

    abstract public function execute(/*...*/) {}

    protected function getContext() {
        return $this->ctx;
    }

    protected function dispatch($config) {
        OptionCallback::dispatch($this->getOptions(), $config);
    }

    protected function dispatchAll($config) {
        OptionCallback::dispatchAll($this->getOptions(), $config);
    }

    protected function getOptions() {
        $this->getContext()->getOptions();
    }

    protected function quit() {
        $this->getContext()->quit();
    }
}
