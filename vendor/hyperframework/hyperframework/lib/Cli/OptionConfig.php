<?php
namespace Hyperframework\Cli;

class OptionConfig {
    private $name;
    private $shortName;
    private $description;
    private $isRepeatable;
    private $isRequired;
    private $hasArgument;
    private $getArgumentName;
    private $enumerationValues;

    public function __construct(
        $name,
        $shortName,
        $description,
        $isRepeatable,
        $isRequired,
        $hasArgument,
        $argumentName = null,
        array $enumerationValues = null
    ) {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->description = $description; 
        $this->isRepeatable = $isRepeatable;
        $this->isRequired = $isRequired;
        $this->hasArgument = $hasArgument;
        $this->enumerationValues = $enumerationValues;
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

    public function getArgumentName() {
        throw new Exception;
    }

    public function getEnumerationValues() {
        return $this->enumerationValues;
    }
}
