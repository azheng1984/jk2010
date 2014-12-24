<?php
namespace Hyperframework\Common;

class StackTraceFormatter {
    public static function format(array $trace, $shouldMergeStackFrame = true) {
        $result = [];
        $index = 0;
        foreach ($trace as $item) {
            $value = '';
            if (isset($item['file']) === false) {
                $value .= '[internal function]: ';
            } else {
                $value .= $item['file'] . '(' . $item['line'] . '): ';
            }
            if (isset($item['class'])) {
                $value .= $item['class'] . $item['type'];
            }
            $value .= $item['function'];
            $arguments = [];
            foreach ($item['args'] as $argument) {
                if (is_string($argument)) {
                    if (mb_strlen($argument) > 15) {
                        $argument = mb_substr($argument, 0, 15) . '...';
                    }
                    $argument = str_replace(
                        ["\\", "'", "\n", "\r", "\t", "\v", "\e", "\f"],
                        ['\\\\', '\\\'', '\n', '\r', '\t', '\v', '\e', '\f'],
                        $argument
                    );
                    $arguments[] = "'$argument'";
                } elseif (is_array($argument)) {
                    $arguments[] = 'Array';
                } elseif (is_null($argument)) {
                    $arguments[] = 'NULL';
                } elseif (is_object($argument)) {
                    $arguments[] = 'Object(' . get_class($argument) . ')';
                } else {
                    $arguments[] = $argument;
                }
            }
            $value .= '(' . implode(', ', $arguments) . ')';
            $result[] = $value;
            ++$index;
        }
        $result[] = '{main}';
        if ($shouldMergeStackFrame) {
            $message = '';
            $index = 0;
            foreach ($result as $item) {
                if ($index !== 0) {
                    $message .= PHP_EOL;
                }
                $message .= '#' . $index . ' ' . $item;
                ++$index;
            }
            return $message;
        }
        return $result;
    }
}
