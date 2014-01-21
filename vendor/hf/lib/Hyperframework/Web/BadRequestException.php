<?php
namespace Hyperframework\Web\ApplicationExceptions;

class BadRequestException extends BaseException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '400 Bad Request', $previous);
    }
}
