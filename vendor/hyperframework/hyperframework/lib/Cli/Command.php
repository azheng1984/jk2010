<?php
namespace Hyperframework\Cli;

use InvalidArgumentException;
use LogicException;
use Hyperframework\Common\InvalidOperationException;

abstract class Command {
    private $app;
    private $isQuitMethodCalled = false;

    public function __construct($app) {
        if ($app === null) {
            throw new InvalidArgumentException(
                "Argument 'app' cannot be null."
            );
        }
        $this->app = $app;
    }

    public function getApp() {
        if ($this->app === null) {
            throw new LogicException(
                "The app equals null, the constructor method of class '"
                    . __CLASS__ . "' is not called."
            );
        }
        return $this->app;
    }

    public function getArguments() {
        $app = $this->getApp();
        $app->getArguments();
    }

    public function hasOption($name) {
        $app = $this->getApp();
        return $app->hasOption($name);
    }

    public function getOption($name) {
        $app = $this->getApp();
        return $this->getApp()->getOption($name);
    }

    public function getOptions() {
        $app = $this->getApp();
        return $this->getApp()->getOptions();
    }

    public function hasGlobalOption($name) {
        $app = $this->getApp();
        return $app->hasGlobalOption($name);
    }

    public function getGlobalOption($name) {
        $app = $this->getApp();
        return $app->getGlobalOption($name);
    }

    public function getGlobalOptions() {
        $app = $this->getApp();
        return $app->getGlobalOptions();
    }

    public function quit() {
        if ($this->isQuitMethodCalled) {
            throw new InvalidOperationException(
                "The quit method of class '" . __CLASS__
                    . "' cannot be called more than once."
            );
        }
        $this->isQuitMethodCalled = true;
        $app = $this->getApp();
        $app->quit();
    }
}
