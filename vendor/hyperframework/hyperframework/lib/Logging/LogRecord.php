<?php
namespace Hyperframework\Logging;

use DateTime;
use DateTimeZone;

class LogRecord {
    private $time;
    private $level;
    private $message;

    public function __construct(array $data) {
        if (isset($data['time']) !== false) {
            if (is_int($data['time'])) {
                $time = new DateTime;
                $time->setTimestamp($data['time']);
                $this->time = $time;
            } elseif (is_float($data['time'])) {
                $this->time = $this->convertStringToDateTime(
                    sprintf('%.6F', $data['time'])
                );
            } elseif ($data['time'] instanceof DateTime === false) {
                $type = gettype($data['time']);
                if ($type === 'object') {
                    $type = get_class($data['time']);
                }
                throw new LoggingException(
                    'Log time must be a DateTime or an integer timestamp or'
                        . " a float timestamp, $type given."
                );
            } else {
                $this->time = $data['time'];
            }
        } else {
            $segments = explode(' ', microtime());
            $this->time = $this->convertStringToDateTime(
                $segments[1] . '.' . (int)($segments[0] * 1000000)
            );
        }
        if (isset($data['level']) === false) {
            throw new LoggingException("Log level is missing.");
        }
        $this->level = $data['level'];
        if (isset($data['message'])) {
            $this->message = $data['message'];
        }
    }

    public function getTime() {
        return $this->time;
    }

    public function getLevel() {
        return $this->level;
    }

    public function getMessage() {
        return $this->message;
    }

    private function convertStringToDateTime($string) {
        $result = DateTime::createFromFormat('U.u', $string);
        $result->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        return $result;
    }
}
