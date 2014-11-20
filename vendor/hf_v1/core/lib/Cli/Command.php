<?php
namespace Hyperframework\Cli;

abstract class Command {
    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

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

    protected function getArguments() {
        return $this->getApp()->getArguments();
    }

    protected function getOptions() {
        return $this->getApp()->getOptions();
    }

    protected function getParentOptions() {
        return $this->getApp()->getParentOptions();
    }

    protected function getCommandParser() {
    }
}
