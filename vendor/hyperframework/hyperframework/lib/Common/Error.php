<?php
namespace Hyperframework\Common;

class Error {
    private $isFatal;
    private $message;
    private $type;
    private $file;
    private $line;
    private $context;
    private $trace;

    public function __construct(
        $type = E_ERROR,
        $message = '',
        $file = __FILE__,
        $line = __LINE__,
        array $context = null,
        array $trace = null,
        $isFatal = null
    ) {
        $this->message = $message;
        $this->type =  $type;
        $this->file =  $file;
        $this->line = $line;
        $this->context = $context;
        $this->trace = $trace;
        $this->isFatal = $isFatal;
        echo 'xx';
        var_dump($this->isRealFatal());
    }

    public function getMessage() {
        return $this->message;
    }

    public function getType() {
        return $this->type;
    }

    public function getTypeAsString() {
        switch ($this->type) {
            case E_DEPRECATED:        return 'Deprecated';
            case E_USER_DEPRECATED:   return 'User Deprecated';
            case E_NOTICE:            return 'Notice';
            case E_USER_NOTICE:       return 'User Notice';
            case E_STRICT:            return 'Strict Standards';
            case E_WARNING:           return 'Warning';
            case E_USER_WARNING:      return 'User Warning';
            case E_COMPILE_WARNING:   return 'Compile Warning';
            case E_CORE_WARNING:      return 'Core Warning';
            case E_USER_ERROR:        return 'User Error';
            case E_RECOVERABLE_ERROR: return 'Catchable Fatal Error';
            case E_COMPILE_ERROR:     return 'Compile Error';
            case E_PARSE:             return 'Parse Error';
            case E_ERROR:             return 'Error';
            case E_CORE_ERROR:        return 'Core Error';
        }
    }

    public function getFile() {
        return $this->file;
    }

    public function getLine() {
        return $this->line;
    }

    public function getTrace() {
        return $this->trace;
    }

    public function getTraceAsString() {
        $trace = $this->getTrace();
        if ($trace === null) {
            return 'undefined';
        } else {
            $result = '';
            $index = 0;
            foreach ($trace as $item) {
                $result .= PHP_EOL . '#' . $index . ' '
                    . $item['file'] . '(' . $item['line'] . '): '
                    . $item['function'];
                ++$index;
            }
            return $result;
        }
    }

    public function getContext() {
        return $this->context;
    }

    public function isFatal() {
        echo '!!!';
        var_dump($this->isFatal);
        if ($this->isFatal === null) {
            $this->isFatal = $this->isRealFatal();
        }
        return $this->isFatal;
    }

    public function isRealFatal() {
        return in_array($this->getType(), array(
            E_ERROR,
            E_USER_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR
        ));
    }

    public function __toString() {
        $result = $this->getTypeAsString();
        if ($this->isFatal() === true && $this->isRealFatal()) {
            $result .= '(Fatal)';
        }
        return $result . ': ' . $this->getMessage() . ' in '
            . $this->getFile() . ' on line ' . $this->getLine();
    }
}
