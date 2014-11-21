<?php
class MultipleCommandApp extends App {
    public function hasMultipleCommand() {
        return true;
    }

    public function hasSubcommand() {
    }

    public function getGlobalOption() {
    }

    protected function executeCommand() {
        if ($this->hasGlobalOption('--version')) {
            $this->renderVersion();
            return;
        }
        $class = Hyperframework\APP_ROOT_NAMESPACE . '\Command';
        $command = new $class($class);
        if ($this->hasGlobalOption('--help')) {
            //global help
        }
        if ($this->hasOption('--help')) {
            $command->renderHelp();
        } else {
            call_user_method_array(
                'execute', $command, $this->commandParser->getArguments()
            );
        }
    }
}
