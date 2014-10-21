<?php
namespace Hyperframework\Cli;

abstract class AbstructCommand extends ExecutableElement {
    protected function getArguments() {
        $this->getContext()->getArguments();
    }
}
