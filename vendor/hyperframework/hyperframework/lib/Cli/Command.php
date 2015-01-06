<?php
namespace Hyperframework\Cli;

abstract class Command {
    private $app;

    public function __construct($app) {
        if ($app === null) {
            throw new Exception("参数 'app' 不允许为 null");
        }
        $this->app = $app;
    }

    protected function getApp() {
        if ($this->app === null) {
            throw new Exception(
                "Class '" . __CLASS__ . "' 构造函数没有被调用."
            );
        }
        return $this->app;
    }

    protected function getArguments() {
        $app = $this->getApp();
        $app->getArguments();
    }

    protected function hasOption($name) {
        $app = $this->getApp();
        return $app->hasOption($name);
    }

    protected function getOption($name) {
        $app = $this->getApp();
        return $this->getApp()->getOption($name);
    }

    protected function getOptions() {
        $app = $this->getApp();
        return $this->getApp()->getOptions();
    }

    protected function hasGlobalOption($name) {
        $app = $this->getApp();
        return $app->hasGlobalOption($name);
    }

    protected function getGlobalOption($name) {
        $app = $this->getApp();
        return $app->getGlobalOption($name);
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
