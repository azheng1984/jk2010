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
    }

    public function getMessage() {
        return $this->message;
    }

    public function getType() {
        return $this->type;
    }

    public function getTypeAsString() {
        switch ($this->type) {
            case E_STRICT:            return 'strict';
            case E_DEPRECATED:        return 'deprecated';
            case E_USER_DEPRECATED:   return 'user deprecated';
            case E_NOTICE:            return 'notice';
            case E_ERROR:             return 'error';
            case E_USER_NOTICE:       return 'user notice';
            case E_USER_ERROR:        return 'user error';
            case E_WARNING:           return 'warning';
            case E_USER_WARNING:      return 'user warning';
            case E_COMPILE_WARNING:   return 'compile warning';
            case E_CORE_WARNING:      return 'core warning';
            case E_RECOVERABLE_ERROR: return 'recoverable error';
            case E_PARSE:             return 'parse';
            case E_COMPILE_ERROR:     return 'compile error';
            case E_CORE_ERROR:        return 'core error';
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

    public function getContext() {
        return $this->context;
    }

    public function isFatal() {
        if ($this->isFatal === null) {
            $this->isFatal = $this->isRealFatal();
        }
        return $this->isFatal;
    }

    public function isRealFatal() {
        return in_array($this->getType(), array(
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR
        ));
    }
}
