<?php
namespace Hyperframework\Cli;

abstract class ExecutableComponent {
    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    abstract public function execute(/*...*/);

    abstract protected function getOptions();

    protected function getApp() {
        return $this->app;
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
        $this->getApp()->quit();
    }
}
