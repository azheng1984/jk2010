<?php
namespace Hyperframework\Logging;

use DateTime;
use Hyperframework\Common\Config;
use Hyperframework\Common\FileLoader;

class LogHandler {
    private $protocol;
    private $path;

    public function handle($level, array $params) {
        $content = $this->format($level, $params);
        $this->write($content);
    }

    public function getPath() {
        if ($this->path === null) {
            $this->initializePath();
        }
        return $this->path;
    }

    public function setPath($value) {
        $this->path = $value;
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

    private function getProtocol() {
        if ($this->protocol === null) {
            $this->initializePath();
        }
        return $this->protocol;
    }

    private function getTimestamp($time) {
        $format = 'Y-m-d H:i:s';
        if (is_int($time)) {
            return date($format, $time);
        } elseif ($time === null) {
            $time = new Datetime;
        }
        return $time->format($format);
    }

    private function initializePath() {
        if ($this->path === null) {
            $this->path = Config::getString(
                'hyperframework.logging.log_path', ''
            );
        }
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
