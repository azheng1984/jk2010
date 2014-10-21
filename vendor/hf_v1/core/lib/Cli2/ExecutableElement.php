<?php
namespace Hyperframework\Cli;

abstract class ExecutableElement {
    private $ctx;

    public function __construct($ctx) {
        $this->ctx = $ctx;
    }

    abstract public function execute(/*...*/);

    abstract protected function getOptions();

    protected function getContext() {
        return $this->ctx;
    }

    protected function dispatch($config) {
        OptionCallback::dispatch($this->getOptions(), $config);
    }

    protected function dispatchAll($config) {
        OptionCallback::dispatchAll($this->getOptions(), $config);
    }

    protected function stopDispatch($level = 1) {
    }

    protected function quit() {
        $this->getContext()->quit();
    }
}
