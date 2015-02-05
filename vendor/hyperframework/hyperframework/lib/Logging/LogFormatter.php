<?php
namespace Hyperframework\Logging;

use DateTime;

class LogFormatter {
    public function format($logRecord) {
        $time = $logRecord->getTime();
        $result = $time->format('Y-m-d H:i:s')
            . ' | ' . $logRecord->getLevel()
            . ' | ' . $logRecord->getName();
        $message = (string)$logRecord->getMessage();
        if ($message !== '') {
            $result .= ' |';
            $this->appendValue($result, $message);
        }
        $extraData = $logRecord->getExtraData();
        if ($extraData !== null) {
            $result .= $this->convert($extraData);
        }
        return $result . PHP_EOL;
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
