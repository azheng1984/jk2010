<?php
namespace Hyperframework\Cli;

abstract class Command extends ExecutableElement {
    protected function getArguments() {
        return $this->getApp()->getArguments();
    }

    protected function getOptions() {
        return $this->getApp()->getOptions();
    }

    protected function getParentOptions() {
    }
}
