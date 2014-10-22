<?php
namespace Hyperframework\Cli;

abstract class AbstructCommand extends ExecutableElement {
    protected function getArguments() {
        return CommandParser::getArguments();
    }

    protected function getOptions() {
        return CommandParser::getCommandOptions();
    }
}
