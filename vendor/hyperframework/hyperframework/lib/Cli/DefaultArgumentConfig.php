<?php
namespace Hyperframework\Cli;

class DefaultArgumentConfig extends ArgumentConfig {
    private $name;
    private $pattern;

    public function __construct($argumentReflector) {
        parent::__construct(
            null,
            $argumentReflector->isOptional(),
            $argumentReflector->isArray()
        );
        $this->name = $argumentReflector->getName();
    }

    public function getPattern() {
        $isRepeatable = $param->isArray();
        $name = $param->getName();
        $length = strlen($name);
        $words = [];
        $word = '';
        for ($index = 0; $index < $length; ++$index) {
            $char = $name[$index];
            if ($char === '_') {
                if ($word !== '') {
                }
            }
            $ascii = ord();
            if (($ascii > 64 && $ascii < 91) || ($ascii > 47 && $ascii < 58)) {
                $word .= $name[$index];
            } else {
                $words[] = $word;
            }
        }
        if ($isRepeatable) {
        }
        $results[] = [
            'name' => new RepeatableArgument()function() {
                //lazy load
            };
            'is_optional' => $param->isOptional(),
            'is_repeatable' => $isRepeatable
        ];
        //convert
        if ($this->isRepeatable()) {
        }
    }
}
