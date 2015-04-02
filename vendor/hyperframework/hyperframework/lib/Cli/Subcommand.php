<?php
namespace Hyperframework\Cli;

abstract class Subcommand extends Command {
    /**
     * @param IMultipleCommandApp $app
     */
    public function __construct(IMultipleCommandApp $app) {
        parent::__construct($app);
    }

    /**
     * return IMultipleCommandApp
     */
    public function getApp() {
        return parent::getApp();
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
}
