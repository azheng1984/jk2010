<?php
namespace Hyperframework\Cli;

class ArgumentConfig {
    private $name;
    private $isRequired;
    private $isRepeatable;

    public function __construct($name, $isRequired, $isRepeatable) {
        $this->name = $name;
        $this->isRequired = $isRequired;
        $this->isRepeatable = $isRepeatable;
    }

    public function getName() {
        return $this->name;
    }

    public function isRequired() {
        return $this->isRequired;
    }

    public function isRepeatable() {
        return $this->isRepeatable;
    }
}
