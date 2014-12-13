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
            if ((string)$this->argumentPattern === '' ||
                preg_match('/^[a-zA-Z0-9-|]+$/', $this->argumentPattern) !== 1
            ) {
                $this->values = false;
            } else {
                $this->values = explode('|', $this->argumentPattern);
            }
        }
        if ($this->values === false) {
            return;
        }
        return $this->values;
    }
}
