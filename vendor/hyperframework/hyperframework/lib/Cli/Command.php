<?php
namespace Hyperframework\Cli;

abstract class Command {
    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    protected function getApp() {
        if ($this->app === null) {
            throw new Exception;
        }
        return $this->app;
    }

    protected function getArguments() {
        return $this->getApp()->getArguments();
    }

    protected function hasOption($name) {
        return $this->getApp()->hasOption($name);
    }

    protected function getOption($name) {
        return $this->getApp()->getOption($name);
    }

    protected function getOptions() {
        return $this->getApp()->getOptions();
    }

    protected function hasGlobalOption($name) {
        return $this->getApp()->hasGlobalOption($name);
    }

    protected function getGlobalOption($name) {
        return $this->getApp()->getGlobalOption($name);
    }

    protected function getGlobalOptions() {
        return $this->getApp()->getGlobalOptions();
    }

    protected function quit() {
        $this->getApp()->quit();
    }
}
