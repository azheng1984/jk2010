<?php
namespace Hyperframework\Common;

use ErrorException as Base;

class ErrorException extends Base {
    private $isFatal;

    public function __construct(
        $message = '',
        $code = 0,
        $severity = 1,
        $file = __FILE__,
        $line = __LINE__,
        $isFatal = null,
        $previous = null
    ) {
        parent::__construct(
            $message, $code, $severity, $file, $line, $previous
        );
        $this->isFatal = $isFatal;
    }

    public function isFatal() {
        if ($this->isFatal === null) {
            $this->isFatal = ErrorCodeHelper::isFatal($this->getSeverity());
        }
        return $this->isFatal;
    }

    public function __toString() {
        $message = 'exception \'' . get_called_class(). '\' with message \''
            . $this->getMessage() . '\' in ' . $this->getFile() . ':'
            . $this->getLine()
            . PHP_EOL . 'Stack trace:';
        if ($this->isFatal()) {
            $message .= 'undefined';
        } else {
            $stackTrace = $this->getTrace();
            array_shift($stackTrace);
            $index = 0;
            foreach ($stackTrace as $trace) {
                $message .= PHP_EOL . '#' . $index . ' '
                    . $trace['file'] . '(' . $trace['line'] . '): '
                    . $trace['function'];
                ++$index;
            }
        }
        return $message;
    }
}
