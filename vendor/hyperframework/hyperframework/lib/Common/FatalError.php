<?php
namespace Hyperframework\Common;

class FatalError extends Error {
    public function __construct($message, $severity, $file, $line) {
        parent::__construct($message, $severity, $file, $line);
    }
}
