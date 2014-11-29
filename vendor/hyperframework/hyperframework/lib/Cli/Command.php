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
        $app = $this->getApp();
        return $app->getArguments();
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
        $app = $this->getApp();
        return $app->getOptions();
    }

    protected function hasGlobalOption($name) {
        $globalOptions = $this->getGlobalOptions();
        return isset($globalOptions[$name]);
    }

    protected function getGlobalOption($name) {
        $globalOptions = $this->getGlobalOptions();
        return $globalOptions[$name];
    }

    protected function getGlobalOptions() {
        $app = $this->getApp();
        return $app->getGlobalOptions();
    }

    protected function quit() {
        $app = $this->getApp();
        $app->quit();
    }
}
