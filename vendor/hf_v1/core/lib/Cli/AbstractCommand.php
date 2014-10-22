<?php
namespace Hyperframework\Cli;

abstract class AbstructCommand extends ExecutableElement {
    protected function getArguments() {
        return $this->getApp()->getArguments();
    }

    protected function getOptions() {
        return $this->getApp()->getCommandOptions();
    }
}
