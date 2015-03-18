<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Inflector;

class DefaultArgumentConfig extends ArgumentConfig {
    private $parameterName;
    private $name;

    public function __construct($reflectionParameter) {
        parent::__construct(
            null,
            !$reflectionParameter->isOptional(),
            $reflectionParameter->isArray()
        );
        $this->parameterName = $reflectionParameter->getName();
    }

    public function getName() {
        if ($this->name !== null) {
            return $this->name;
        }
        $words = [];
        $word = '';
        $length = strlen($this->parameterName);
        for ($index = 0; $index < $length; ++$index) {
            $char = $this->parameterName[$index];
            $ascii = ord($char);
            if ($char !== '_' && ($ascii < 65 || $ascii > 90)) {
                $word .= $this->parameterName[$index];
            } else {
                if ($word !== '') {
                    $words[] = $word;
                    $word = '';
                }
                if ($char !== '_') {
                    $word = strtolower($char);
                }
            }
        }
        if ($word !== '') {
            if ($this->isRepeatable() && ctype_alpha($word)) {
                if ($word !== 'list'
                    && $word !== 'array'
                    && $word !== 'collection'
                ) {
                    $words[] = Inflector::singularize($word);
                } elseif (count($words) === 0) {
                    $words[] = 'element';
                }
            } else {
                $words[] = $word;
            }
        }
        $this->name = implode('-', $words);
        return $this->name;
    }
}
