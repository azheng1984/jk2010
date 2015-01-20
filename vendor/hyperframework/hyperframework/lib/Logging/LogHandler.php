<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Config;
use Hyperframework\Common\FileLoader;
use DateTime;

class LogHandler {
    private $protocol;
    private $path;

    public function handle($level, array $params) {
        $content = $this->format($level, $params);
        $this->write($content);
    }

    protected function write($content) {
        $flag = null;
        if ($this->getProtocol() === 'file') {
            $flag = FILE_APPEND | LOCK_EX;
        }
        file_put_contents($this->getPath(), $content, $flag);
    }

    protected function format($level, array $params) {
        $time = isset($params['time']) ? $params['time'] : null;
        $name = isset($params['name']) ? $params['name'] : null;
        $message = isset($params['message']) ? $params['message'] : null;
        $data = isset($params['data']) ? $params['data'] : null;
        $count = count($params);
        $result = $this->getTimestamp($time) . ' | ' . $level;
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
        if ($data !== null) {
            $result .= $this->convert($data);
        }
        return $result . PHP_EOL;
    }

    protected function getPath() {
        if ($this->path === null) {
            $this->initializePath();
        }
        return $this->path;
    }

    protected function getProtocol() {
        if ($this->protocol === null) {
            $this->initializePath();
        }
        return $this->protocol;
    }

    private function getTimestamp($time) {
        //todo config format
        if ($time === null) {
             $time = new DateTime;
        }
        if (is_int($time)) {
            return date('Y-m-d h:i:s', $time);
        } elseif ($time instanceof DateTime) {
            return $time->format('Y-m-d h:i:s');
        } else {
            return $time;
        }
    }

    private function initializePath() {
        $this->path = Config::getString(
            'hyperframework.log_handler.log_path', ''
        );
        $this->protocol = 'file';
        if ($this->path === '') {
            $this->path = 'log' . DIRECTORY_SEPARATOR . 'app.log';
        } else {
            if (preg_match('#^([a-zA-Z0-9.+]+)://#', $this->path, $matches)) {
                $this->protocol = strtolower($matches[1]);
                return;
            }
        }
        $this->path = FileLoader::getFullPath($this->path);
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
            if (preg_match('/^[0-9a-zA-Z_]+$/', $key) === 0) {
                throw new LoggingException(
                    "Log entry field '$key' is invalid."
                );
            }
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
