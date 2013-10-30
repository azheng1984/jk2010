<?php
namespace Hyperframework\Cli;

class OptionObjectBuilder {
    private $config;
    private $argumentReader;

    public function __construct($config, $reader) {
        $this->config = $config;
        $this->argumentReader = new OptionArgumentReader($reader);
    }

    public function build() {
        $reflector = $this->getConstructorReflection();
        $arguments = $this->getArguments($reflector->getConstructor());
        if (count($arguments) === 0) {
            return new $this->config['class'];
        }
        return $reflector->newInstanceArgs($arguments);
    }

    private function getConstructorReflection() {
        try {
            return new ReflectionClass($this->config['class']);
        } catch (ReflectionException $excpetion) {
            throw new CommandException($excpetion->getMessage());
        }
    }

    private function getArguments($constructor) {
        $maximumLength = 0;
        if ($constructor !== null) {
            $maximumLength = $this->getMaximumLength($constructor);
        }
        $arguments = $this->argumentReader->read($maximumLength);
        $length = count($arguments);
        $verifier = new ArgumentVerifier;
        $verifier->verify($constructor, $length, $maximumLength === null);
        return $arguments;
    }

    private function getMaximumLength($constructor) {
        if (in_array('infinite', $this->config, true)) {
            return;
        }
        return $constructor->getNumberOfParameters();
    }
}
