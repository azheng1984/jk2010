<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class OptionConfig {
    private $name;
    private $shortName;
    private $description;
    private $isRepeatable;
    private $isRequired;
    private $argumentConfig;

    public function __construct(
        $name,
        $shortName,
        $description,
        $isRepeatable,
        $isRequired,
        $argumentConfig
    ) {
        $this->name = $name;
        if ($shortName === 'W') {
            throw new ConfigException(
                'The -W (capital-W)'
                    . ' option must be reserved for implementation extensions.'
            );
        }
        $this->shortName = $shortName;
        $this->description = $description;
        $this->isRepeatable = $isRepeatable;
        $this->isRequired = $isRequired;
        $this->argumentConfig = $argumentConfig;
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

    public function getArgumentConfig() {
        return $this->argumentConfig;
    }
}
