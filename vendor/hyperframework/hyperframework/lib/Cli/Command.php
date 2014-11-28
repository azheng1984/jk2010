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

    protected function getArguments() {
        return $this->getApp()->getArguments();
    }

    protected function hasOption($name) {
        $options = $this->getOptions();
        return isset($options[$name]);
    }

    protected function getOption($name) {
        $options = $this->getOptions();
        return $options[$name];
    }

    protected function getOptions() {
        return $this->getApp()->getOptions();
    }

    protected function hasGlobalOption($name) {
        $options = $this->getGlobalOptions();
        return isset($options[$name]);
    }

    protected function getGlobalOption($name) {
        $options = $this->getGlobalOptions();
        return $options[$name];
    }

    protected function getGlobalOptions() {
        if ($this->getApp()->hasMultipleCommands()) {
            return $this->getApp()->getGlobalOptions();
        } else {
            throw new Exception;
        }
    }

    protected function quit() {
        $this->getApp()->quit();
    }
}
