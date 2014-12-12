<?php
namespace Hyperframework\Cli;

class ArgumentConfig {
    private $pattern;
    private $isOptional;
    private $isRepeatable;

    public function __construct($pattern, $isOptional, $isRepeatable) {
        // [(<key>=<value>)...]
        $this->pattern = $pattern;
        $this->isOptional = $isOptional;
        $this->isRepeatable = $isRepeatable;
    }

    public function getPattern() {
        return $this->pattern;
    }

    public function isOptional() {
        return $this->isOptional;
    }

    public function isRepeatable() {
        return $this->isRepeatable;
    }
}
