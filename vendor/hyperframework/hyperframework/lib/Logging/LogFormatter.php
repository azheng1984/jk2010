<?php
namespace Hyperframework\Logging;

class LogFormatter {
    /**
     * @param object $logRecord
     * @return string
     */
    public function format($logRecord) {
        $time = $logRecord->getTime();
        $result = $time->format('Y-m-d H:i:s')
            . ' [' . LogLevel::getName($logRecord->getLevel()) . ']';
        $message = (string)$logRecord->getMessage();
        if ($message !== '') {
            $result .= ' ' . $message;
        }
        return $result . PHP_EOL;
    }
}
