<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Inflector;

class DefaultArgumentConfig extends ArgumentConfig {
    private $parameterName;
    private $name;

    public function __construct($reflectionParameter) {
        parent::__construct(
            null,
            $reflectionParameter->isOptional(),
            $reflectionParameter->isArray()
        );
        $this->parameterName = $reflectionParameter->getName();
    }

    public function getName() {
        $words = [];
        $name = $this->parameterName;
        $word = '';
        $length = strlen($name);
        for ($index = 0; $index < $length; ++$index) {
            $char = $name[$index];
            $ascii = ord($char);
            if ($char !== '_' && ($ascii < 65 || $ascii > 90)) {
                $word .= $name[$index];
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
                    && $word !== 'queue'
                    && $word !== 'stack'
                ) {
                    $words[] = Inflector::singularize($word);
                } elseif (count($words) === 0) {
                    $words[] = 'element';
                }
            } else {
                $words[] = $word;
            }
        }
        return implode('-', $words);
    }
}
