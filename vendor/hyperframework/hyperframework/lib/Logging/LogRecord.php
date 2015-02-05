<?php
namespace Hyperframework\Logging;

use DateTime;

class LogRecord {
    private $time;
    private $level;
    private $name;
    private $message;
    private $extraData;

    public function __construct(array $data) {
        if (isset($data['time']) !== false) {
            if (is_int($data['time'])) {
                $time = new DateTime;
                $time->setTimestamp($data['time']);
                $this->time = $time;
            } elseif ($data['time'] instanceof DateTime === false) {
                $type = gettype($data['time']);
                if ($type === 'object') {
                    $type = get_class($data['time']);
                }
                throw new LoggingException(
                    "Log time must be a DateTime or an integer timestamp, "
                        . $type . " given."
                );
            } else {
                $this->time = $data['time'];
            }
        } else {
            $this->time = new DateTime;
        }
        if (isset($data['level']) === false) {
            throw new LoggingException("Log level is missing.");
        }
        $this->level = $data['level'];
        if (isset($data['name'])) {
            if (preg_match('/^[a-zA-Z0-9_.]+$/', $data['name']) === 0
                || $data['name'][0] === '.'
                || substr($data['name'], -1) === '.'
            ) {
                throw new LoggingException(
                    "Log name '{$data['name']}' is invalid."
                );
            }
        } else {
            throw new LoggingException("Log name is missing.");
        }
        $this->name = $data['name'];
        if (isset($data['message'])) {
            if (is_array($data['message'])) {
                $count = count($data['message']);
                if ($count === 0) {
                    $data['message'] = '';
                } elseif ($count === 1) {
                    $data['message'] = $data['message'][0];
                } else {
                    $data['message'] =
                        call_user_func_array('sprintf', $data['message']);
                }
            }
            $this->message = (string)$data['message'];
        }
        unset($data['time']);
        unset($data['level']);
        unset($data['name']);
        unset($data['message']);
        if (count($data) > 0) {
            self::checkExtraData($data);
            $this->extraData = $data;
        }
    }

    public function getTime() {
        return $this->time;
    }

    public function getLevel() {
        return $this->level;
    }

    public function getName() {
        return $this->name;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getExtraData() {
        return $this->extraData;
    }

    private static function checkExtraData(array $data) {
        foreach ($data as $key => $value) {
            if (preg_match('/^[0-9a-zA-Z_]+$/', $key) === 0) {
                throw new LoggingException("Log key '$key' is invalid.");
            }
            if (is_array($value)) {
                self::checkExtraData($value);
            }
        }
    }
}
