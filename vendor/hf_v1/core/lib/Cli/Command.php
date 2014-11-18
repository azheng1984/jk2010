<?php
namespace Hyperframework\Cli;

abstract class Command extends Executor {
    protected function getArguments() {
        return $this->getApp()->getArguments();
    }

    protected function getOptions() {
        return $this->getApp()->getCommandOptions();
    }
}
