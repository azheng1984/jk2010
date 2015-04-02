<?php
namespace Hyperframework\Cli;

use InvalidArgumentException;
use LogicException;
use Hyperframework\Common\InvalidOperationException;

abstract class Command {
    private $app;
    private $isQuitMethodCalled = false;

    /**
     * @param IApp $app
     */
    public function __construct(IApp $app) {
        $this->app = $app;
    }

    /**
     * @return IApp
     */
    public function getApp() {
        if ($this->app === null) {
            throw new LogicException(
                "The app equals null, the constructor method of class '"
                    . __CLASS__ . "' is not called."
            );
        }
        return $this->app;
    }

    /**
     * @return string[]
     */
    public function getArguments() {
        $app = $this->getApp();
        return $app->getArguments();
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function hasOption($name) {
        $app = $this->getApp();
        return $app->hasOption($name);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getOption($name) {
        $app = $this->getApp();
        return $this->getApp()->getOption($name);
    }

    /**
     * @return string[]
     */
    public function getOptions() {
        $app = $this->getApp();
        return $this->getApp()->getOptions();
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
