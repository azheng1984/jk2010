<?php
namespace Hyperframework\Cli;

class CommandRunner {
    public function run($config, $options, $arguments) {
        if (isset($config['commands'])) {
            ExplorerContext::getExplorer('Package')->render($config);
            return;
        }
        $reflector = $this->getReflectionMethod($config);
        $verifier = new ArgumentVerifier;
        $verifier->verify(
            $reflector, count($arguments), in_array('infinite', $config, true)
        );
        $class = 'Hyperframework\Tool\App\\' . $config['class'];
        $reflector->invokeArgs(new $class($options), $arguments);
    }

    private function getReflectionMethod($config) {
        if (!isset($config['class'])) {
            throw new CliException('Command class not defined');
        }
        try {
            return new \ReflectionMethod('Hyperframework\Tool\App\\' . $config['class'], 'execute');
        } catch (\ReflectionException $exception) {
            throw new CliException($exception->getMessage());
        }
    }
}
