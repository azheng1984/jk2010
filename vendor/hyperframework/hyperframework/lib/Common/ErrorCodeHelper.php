<?php
namespace Hyperframework\Common;

class ErrorCodeHelper {
    public static function isFatal($code) {
        return in_array($code, array(
            E_ERROR,
            E_USER_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR
        ));
    }

    public static function toString($code) {
        switch ($code) {
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
}
