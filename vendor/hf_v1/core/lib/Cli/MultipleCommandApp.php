<?php
class MultipleCommandApp extends App {
    public function hasMultipleCommand() {
        return true;
    }

    protected function executeCommand() {
        if ($this->hasSubcommand()) {
            if ($this->hasParentOption('--version')) {
                $this->renderVersion();
                return;
            }
        } else {
            if ($this->hasOption('--version')) {
                $this->renderVersion();
                return;
            }
        }
        if ($this->hasOption('--version')) {
        }
        $class = Hyperframework\APP_ROOT_NAMESPACE . '\Command';
        $command = new $class($class);
        if ($this->hasOption('--help')) {
            $command->renderHelp();
        } else {
            call_user_method_array(
                'execute', $command, $this->commandParser->getArguments()
            );
        }
    }
}
