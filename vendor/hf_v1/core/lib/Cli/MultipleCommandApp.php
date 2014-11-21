<?php
class MultipleCommandApp extends App {
    public function hasMultipleCommands() {
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
        if ($this->hasGlobalOption('--help')) {
            $this->renderGlobalHelp();
        }
        $class = Hyperframework\APP_ROOT_NAMESPACE . '\Command';
        if ($this->hasSubcommand()) {
            $tmp = ucwords(str_replace('-', ' ', $this->getSubcommand()));
            $tmp = str_replace(' ', '', $tmp) . 'Command';
            $class = Hyperframework\APP_ROOT_NAMESPACE . '\\' . $tmp;
        }
        $command = new $class($this);
        if ($this->hasOption('--help')) {
            $command->renderHelp();
        } else {
            call_user_method_array(
                'execute', $command, $this->commandParser->getArguments()
            );
        }
    }

    protected function renderGlobalHelp() {
        //render default global help
    }
}
