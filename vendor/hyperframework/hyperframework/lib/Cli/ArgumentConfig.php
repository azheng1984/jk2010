<?php
namespace Hyperframework\Cli;

class ArgumentConfig {
    private $name;
    private $isOptional;
    private $isRepeatable;

    public function __construct($name, $isOptional, $isRepeatable) {
        $this->name = $name;
        $this->isOptional = $isOptional;
        $this->isRepeatable = $isRepeatable;
    }

    public function getName() {
        return $this->name;
    }

    public function isOptional() {
        return $this->isOptional;
    }

    public function isRepeatable() {
        return $this->isRepeatable;
    }
}
