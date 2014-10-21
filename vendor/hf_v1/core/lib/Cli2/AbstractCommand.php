<?php
namespace Hyperframework\Cli;

abstract class AbstructCommand extends Executor {
    protected function getArguments() {
        $this->getContext()->getArguments();
    }
}
