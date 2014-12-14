<?php
namespace Hyperframework\Cli;

class OptionConfig {
    private $name;
    private $shortName;
    private $description;
    private $isRepeatable;
    private $isRequired;
    private $hasArgument;
    private $argumentPattern;
    private $values;

    public function __construct(
        $name,
        $shortName,
        $description,
        $isRepeatable,
        $isRequired,
        $hasArgument,
        $argumentPattern = null
    ) {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->description = $description;
        $this->isRepeatable = $isRepeatable;
        $this->isRequired = $isRequired;
        $this->hasArgument = $hasArgument;
        $this->argumentPattern = $argumentPattern;
    }

    public function getName() {
        return $this->name;
    }

    public function getShortName() {
        return $this->shortName;
    }

    public function getDescription() {
        return $this->description;
    }

    public function isRepeatable() {
        return $this->isRepeatable;
    }

    public function isRequired() {
        return $this->isRequired;
    }

    public function hasArgument() {
        return $this->hasArgument;
    }

    public function getArgumentPattern() {
        return $this->argumentPattern;
    }

    public function getValues() {
        if ($this->values === null) {
            $pattern = (string)$this->argumentPattern;
            if ($pattern !== '') {
                $pattern = ltrim($this->argumentPattern, '(');
                $pattern = rtrim($pattern, ')');
                if ($pattern === ''
                    || preg_match('/^[a-zA-Z0-9-_|]+$/', $pattern) !== 1
                ) {
                    $this->values = false;
                } else {
                    $this->values = explode('|', $pattern);
                }
            } else {
                $this->values = false;
            }
        }
        if ($this->values === false) {
            return;
        }
        return $this->values;
    }
}
