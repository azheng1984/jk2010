<?php
namespace Hyperframework\Cli;

abstract class CommandCollection extends ExecutableElement {
    protected function getOptions() {
        return $this->getApp()->getCommandCollectionOptions();
    }
}
