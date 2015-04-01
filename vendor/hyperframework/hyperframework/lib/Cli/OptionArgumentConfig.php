<?php
namespace Hyperframework\Cli;

class OptionArgumentConfig {
    private $name;
    private $isRequired;
    private $values;

    public function __construct($name, $isRequired, $values = null) {
        $this->name = $name;
        $this->isRequired = $isRequired;
        $this->values = $values;
    }

    public function getName() {
        return $this->name;
    }

    public function isRequired() {
        return $this->isRequired;
    }

    public function getValues() {
        return $this->values;
    }
}
