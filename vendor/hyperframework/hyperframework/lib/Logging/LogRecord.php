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
                $this->time = DateTime::createFromFormat(
                    'U.u', sprintf('%.6F', $data['time'])
                );
            } elseif ($data['time'] instanceof DateTime === false) {
                $type = gettype($data['time']);
                if ($type === 'object') {
                    $type = get_class($data['time']);
                }
                throw new LoggingException(
                    "Log time must be a DateTime or an integer timestamp or"
                        . " a float timestamp, "
                        . $type . " given."
                );
            } else {
                $this->time = $data['time'];
            }
        } else {
            $this->time = new Datetime; //DateTime::createFromFormat( 'U', time() //sprintf('%.6F', microtime()));
date_default_timezone_get();
            $this->time = DateTime::createFromFormat( 'U.u', sprintf('%.6F', microtime(true)))->setTimeZone(new DateTimeZone(null));
            //echo  sprintf('%.6F', microtime(true)).PHP_EOL;
            //echo microtime();
            echo $this->time->format('Y:m:d H:i:s');
            exit;
        }
        if (isset($data['level']) === false) {
            throw new LoggingException("Log level is missing.");
        }
        $this->level = $data['level'];
        if (isset($data['message'])) {
            $this->message = $data['message'];
        }
    }

//    private createDateTime($time) {
//        static $timezone = new DateTimeZone(
//            date_default_timezone_get() ?: 'UTC'
//        );
//        return DateTime::createFromFormat(
//            'U.u', sprintf('%.6F', microtime(true))
//        );
//    }

    public function getTime() {
        return $this->time;
    }

    public function getLevel() {
        return $this->level;
    }

    public function getMessage() {
        return $this->message;
    }
}
