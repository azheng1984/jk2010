<?php
namespace Hyperframework\Logging;

use DateTime;
use DateTimeZone;

class LogRecord {
    private $level;
    private $message;
    private $time;

    public function __construct($level, $message, $time = null) {
        if ($time !== null) {
            if (is_int($time)) {
                $timestamp = $time;
                $time = new DateTime;
                $time->setTimestamp($timestamp);
                $this->time = $time;
            } elseif (is_float($time)) {
                $this->time = $this->convertStringToDateTime(
                    sprintf('%.6F', $time)
                );
            } elseif ($time instanceof DateTime === false) {
                $type = gettype($time);
                if ($type === 'object') {
                    $type = get_class($time);
                }
                throw new LoggingException(
                    'Log time must be a DateTime or an integer timestamp or'
                        . " a float timestamp, $type given."
                );
            } else {
                $this->time = $time;
            }
        } else {
            $segments = explode(' ', microtime());
            $this->time = $this->convertStringToDateTime(
                $segments[1] . '.' . (int)($segments[0] * 1000000)
            );
        }
        $this->level = $level;
        $this->message = $message;
    }

    public function getLevel() {
        return $this->level;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getTime() {
        return $this->time;
    }

    private function convertStringToDateTime($string) {
        $result = DateTime::createFromFormat('U.u', $string);
        $result->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        return $result;
    }
}
