<?php
namespace Hyperframework\Web\Exception;

class ForbiddenException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '403 Forbidden', $previous);
    }
}
