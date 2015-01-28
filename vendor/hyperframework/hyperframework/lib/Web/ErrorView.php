<?php
namespace Hyperframework\Web;

class ErrorView {
    public function __construct($exception) {
        return function() use ($exception) {
            include $this->getFullPath();
        };
    }

    public function getException() {
    }
}
