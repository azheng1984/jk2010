<?php
namespace Hyperframework\Logging;

use DateTime;

class LogFormatter {
    public function format($time, $level, $name, $message, array $extra) {
//        $time = isset($options['time']) ? $options['time'] : null;
//        unset($options['time']);
//        $name = isset($options['name']) ? $options['name'] : null;
//        unset($options['name']);
//        $message = isset($options['message']) ? $options['message'] : null;
//        unset($options['message']);
        $result = $this->getTime($time) . ' | ' . $level;
        if ((string)$name !== '') {
            $result .= ' | ' . $name;
        }
        if ((string)$message !== '') {
            if ((string)$name === '') {
                $result .= ' ||';
            } else {
                $result .= ' |';
            }
            $this->appendValue($result, $message);
        }
        if (count($data) !== 0) {
            $result .= $this->convert($data);
        }
        return $result . PHP_EOL;
    }

    private function getTime($time) {
        $format = 'Y-m-d H:i:s';
        if (is_int($time)) {
            return date($format, $time);
        } elseif ($time === null) {
            $time = new Datetime;
        }
        return $time->format($format);
    }

    private function appendValue(&$data, $value, $prefix = "\t>") {
        if (strpos($value, PHP_EOL) === false) {
            $data .= ' ' . $value;
            return;
        }
        if (strncmp($value, PHP_EOL, strlen(PHP_EOL)) !== 0) {
            $value = ' ' . $value;
        }
        $value = str_replace(PHP_EOL, PHP_EOL . $prefix . ' ', $value);
        $value = str_replace(
            PHP_EOL . $prefix . ' ' . PHP_EOL,
            PHP_EOL . $prefix . PHP_EOL,
            $value
        );
        if (substr($value, -1) === ' ') {
            $tail = substr($value, -strlen($prefix) - strlen(PHP_EOL) - 1);
            if ($tail === PHP_EOL . $prefix . ' ') {
                $value = rtrim($value, ' ');
            }
        }
        $data .= $value;
    }

    private function convert(array $data, $depth = 1) {
        $result = null;
        $prefix = str_repeat("\t", $depth);
        foreach ($data as $key => $value) {
            $result .= PHP_EOL . $prefix . $key . ':';
            if (is_array($value)) {
                $result .= $this->convert($value, $depth + 1);
            } elseif ((string)$value !== '') {
                $this->appendValue($result, $value, $prefix . "\t>");
            }
        }
        return $result;
    }
}
