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
            $this->time = DateTime::createFromFormat(
                'U.u', sprintf('%.6F', microtime(true))
            );
        }
        if (isset($data['level']) === false) {
            throw new LoggingException("Log level is missing.");
        }
        $this->level = $data['level'];
        if (isset($data['channel'])) {
            if (preg_match('/^[a-zA-Z0-9_.]+$/', $data['channel']) === 0
                || $data['channel'][0] === '.'
                || substr($data['name'], -1) === '.'
            ) {
                throw new LoggingException(
                    "Log channel '{$data['name']}' is invalid."
                );
            }
        } else {
            throw new LoggingException("Log name is missing.");
        }
        $this->name = $data['name'];
        if (isset($data['message'])) {
            $this->message = $data['message'];
        }
        unset($data['time']);
        unset($data['level']);
        unset($data['message']);
        //23:23:23 | ERROR | sdf.dsff.sdf | [sdfsdf] | dsf is message. | sad=dsf, asdf=dsf]
        //['post_id' => 'xx'];
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
