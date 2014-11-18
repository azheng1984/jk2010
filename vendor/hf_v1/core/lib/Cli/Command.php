<?php
namespace Hyperframework\Cli;

abstract class Command extends ExecutableComponent {
    protected function getArguments() {
        return $this->getApp()->getArguments();
    }

    protected function getOptions() {
        return $this->getApp()->getCommandOptions();
    }
}
