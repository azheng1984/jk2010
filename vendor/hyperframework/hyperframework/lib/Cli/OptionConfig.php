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
        $argumentPattern,
//        array $values = null
    ) {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->description = $description; 
        $this->isRepeatable = $isRepeatable;
        $this->isRequired = $isRequired;
        $this->hasArgument = $hasArgument;
        $this->argumentPattern = $argumentPattern;
//        $this->values = $values;
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
        // -x(ai|bi|ci)
        // -x <name>=<value> //better
        // -x name=value //better
        // -c (<name>=<value>)
        // --article[=(a|b|c)]
        // --article[=<article>]
        // --article[=(<key>=<value>)]
        // --article[=up|down]
        // --article[=(up|down)] //better
        // --article=(up|down) //better
        // -a, --article enable|disable
        // --article=up|down
        // --article[=<a>[<b>][<c>]]
        return $this->values;
    }
}
