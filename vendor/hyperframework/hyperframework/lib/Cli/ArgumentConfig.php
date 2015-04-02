<?php
namespace Hyperframework\Cli;

class ArgumentConfig {
    private $name;
    private $isRequired;
    private $isRepeatable;

    /**
     * @param string $name
     * @param boolean $isRequired
     * @param boolean $isRepeatable
     */
    public function __construct($name, $isRequired, $isRepeatable) {
        $this->name = $name;
        $this->isRequired = $isRequired;
        $this->isRepeatable = $isRepeatable;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isRequired() {
        return $this->isRequired;
    }

    /**
     * @return boolean
     */
    public function isRepeatable() {
        return $this->isRepeatable;
    }
}
