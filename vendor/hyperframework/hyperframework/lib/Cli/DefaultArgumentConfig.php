<?php
namespace Hyperframework\Cli;

class DefaultArgumentConfig extends ArgumentConfig {
    private $originalName;
    private $name;

    public function __construct($argumentReflector) {
        parent::__construct(
            null,
            $argumentReflector->isOptional(),
            $argumentReflector->isArray()
        );
        $this->originalName = $argumentReflector->getName();
    }

    public function getName() {
        return $this->originalName;
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
    }
}
