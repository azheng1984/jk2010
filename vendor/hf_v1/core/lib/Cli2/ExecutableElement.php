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
        if ($this->hasOption('xx')) {
            $xx = $this->getOption('xx');
        }
        return $this->ctx;
    }

    protected function hasOption($name) {
        $options = $this->getOptions();
        return isset($options[$name]);
    }

    protected function getOption($name) {
        $options = $this->getOptions();
        return $options[$name];
    }

    protected function quit() {
        $this->getContext()->quit();
    }
}
