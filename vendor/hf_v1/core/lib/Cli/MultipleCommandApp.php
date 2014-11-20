<?php
class MultipleCommandApp extends App {
    public function hasMultipleCommand() {
        return true;
    }

    protected function executeCommand() {
        if ($this->getCommandParser()->getSubcommand() === null) {
            $class = Hyperframework\APP_ROOT_NAMESPACE . '\Command';
            $command = new $class($class);
            call_user_method_array(
                'execute', $command, $this->commandParser->getArguments()
            );
        }
    }
}
