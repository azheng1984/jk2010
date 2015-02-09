<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Config;
use Hyperframework\Common\FileLoader;

class LogWriter {
    private $path;
    private $isDefaultPath;

    public function write($text) {
        if ($this->path === null) {
            $this->path = Config::getString(
                'hyperframework.logging.log_path', ''
            );
            if ($this->path === '') {
                $this->isDefaultPath = true;
                $this->path = 'log' . DIRECTORY_SEPARATOR . 'app.log';
            } else {
                $this->isDefaultPath = false;
            }
            $this->path = FileLoader::getFullPath($this->path);
            if (file_exists($this->path) === false) {
                $directory = dirname($this->path);
                if (file_exists($directory) === false) {
                    if (mkdir($directory, 0777, true) === false) {
                        throw new LoggingException($this->getErrorMessage(
                            "Failed to create log file '{$this->path}'"
                        ));
                    }
                }
            }
        }
        $handle = fopen($this->path, 'a');
        if ($handle !== false) {
            if (flock($handle, LOCK_EX)) {
                fwrite($handle, $text);
                fflush($handle);
                flock($handle, LOCK_UN);
            } else {
                fclose($handle);
                throw new LoggingException($this->getErrorMessage(
                    "Failed to lock log file '{$this->path}'"
                ));
            }
            fclose($handle);
        } else {
            throw new LoggingException($this->getErrorMessage(
                "Failed to open or create log file '{$this->path}'"
            ));
        }
    }

    private function getErrorMessage($prefix) {
        if ($this->isDefaultPath === false) {
            $prefix .= ", defined in 'hyperframework.logging.log_path'";
        }
        return $prefix . '.';
    }
}
