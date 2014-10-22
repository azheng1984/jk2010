<?php
namespace Hyperframework\Cli;

abstract class AbstructCommandCollection extends ExecutableElement {
    protected function getOptions() {
        return $this->getApp()->getCollectionOptions();
    }
}
