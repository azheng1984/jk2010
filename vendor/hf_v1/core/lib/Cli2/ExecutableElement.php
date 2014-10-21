<?php
namespace Hyperframework\Cli;

abstract class ExecutableElement {
    private $ctx;
    private $dispatchDepth = 0;
    private $stoppedDispatchDepth;

    public function __construct($ctx) {
        $this->ctx = $ctx;
    }

    abstract public function execute(/*...*/);

    abstract protected function getOptions();

    protected function getContext() {
        return $this->ctx;
    }

    protected function dispatch($config) {
        ++$this->dispatchDepth;
        //dispatch
        --$this->dispatchDepth;
    }

    protected function dispatchAll($config) {
        ++$this->dispatchDepth;
        //dispatch
        --$this->dispatchDepth;
    }

    protected function stopDispatch() {
        $this->stoppedDispatchDepth = $this->dispatchDepth;
    }

    protected function quit() {
        $this->getContext()->quit();
    }
}
