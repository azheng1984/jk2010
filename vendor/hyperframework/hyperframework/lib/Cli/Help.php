<?php
namespace Hyperframework\Cli;

class Help {
    private $app;
    private $errorMessage;

    public function __construct($app, $errorMessage = null) {
        $this->app = $app;
        $this->errorMessage = $errorMessage;
    }

    public function render() {
        if ($this->hasErrorMessage()) {
            $this->renderErrorHelp();
            return;
        }
        $this->renderFullHelp();
    }

    protected function hasErrorMessage() {
        return $this->errorMessage !== null;
    }

    protected function getErrorMessage() {
        return $this->errorMessage;
    }

    protected function renderFullHelp() {
        echo 'Usage: ';
    }

    protected function renderCompressedOptions() {
    }

    protected function renderOptions() {
    }

    protected function renderErrorHelp() {
        echo $this->errorMessage . PHP_EOL;
        $this->renderUsage();
        //short version for command parsing exception
    }
}
