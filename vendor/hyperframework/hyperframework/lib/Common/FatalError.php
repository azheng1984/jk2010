<?php
namespace Hyperframework\Common;

class FatalError extends Error {
    public function __construct($severity, $message, $file, $line) {
        parent::__construct($severity, $message, $file, $line);
    }
}
