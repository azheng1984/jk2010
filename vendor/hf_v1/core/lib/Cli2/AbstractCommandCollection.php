<?php
namespace Hyperframework\Cli;

abstract class AbstructCommandCollection extends ExecutableElement {
    protected function getOptions() {
        return CommandParser::getCollectionOptions();
    }
}
