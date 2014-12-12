<?php
namespace Hyperframework\Cli;

class MutuallyExclusiveOptionGroupConfig {
    private $options;
    private $isRequired;

    public function __construct(array $options, $isRequired) {
        $this->options = $options;
        $this->isRequired = $isRequired;
    }

    public function getOptions() {
        return $this->options;
    }

    public function isRequired() {
        return $this->isRequired;
    }
}
