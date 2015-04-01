<?php
namespace Hyperframework\Cli;

class MutuallyExclusiveOptionGroupConfig {
    private $optionConfigs;
    private $isRequired;

    public function __construct($optionConfigs, $isRequired) {
        $this->optionConfigs = $optionConfigs;
        $this->isRequired = $isRequired;
    }

    public function getOptionConfigs() {
        return $this->optionConfigs;
    }

    public function isRequired() {
        return $this->isRequired;
    }
}
