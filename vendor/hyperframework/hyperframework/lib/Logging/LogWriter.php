<?php
namespace Hyperframework\Logging;

use Exception;
use ErrorException;
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
                    try {
                        if (mkdir($directory, 0777, true) === false) {
                            throw new LoggingException($this->getErrorMessage(
                                "Failed to create log file '{$this->path}'"
                            ));
                        }
                    } catch (ErrorException $e) {
                        throw new LoggingException($this->getErrorMessage(
                            "Failed to create log file '{$this->path}'"
                        ), 0, $e);
                    }
                }
            }
        }
        try {
            $handle = fopen($this->path, 'a');
        } catch (ErrorException $e) {
            throw new LoggingException($this->getErrorMessage(
                "Failed to open or create log file '{$this->path}'"
            ), 0, $e);
        }
        if ($handle === false) {
            throw new LoggingException($this->getErrorMessage(
                "Failed to open or create log file '{$this->path}'"
            ));
        }
        try {
            try {
                if (flock($handle, LOCK_EX) === false) {
                    throw new LoggingException($this->getErrorMessage(
                        "Failed to lock log file '{$this->path}'"
                    ));
                }
            } catch (ErrorException $e) {
                throw new LoggingException($this->getErrorMessage(
                    "Failed to lock log file '{$this->path}'"
                ), 0, $e);
            }
            try {
                $status = fwrite($handle, $text);
                if ($status !== false) {
                    $status = fflush($handle);
                }
                if ($status === false) {
                    flock($handle, LOCK_UN);
                    throw new LoggingException($this->getErrorMessage(
                        "Failed to write log file '{$this->path}'"
                    ));
                }
            } catch (ErrorException $e) {
                flock($handle, LOCK_UN);
                throw new LoggingException($this->getErrorMessage(
                    "Failed to write log file '{$this->path}'"
                ), 0, $e);
            }
            flock($handle, LOCK_UN);
        } catch (Exception $e) {
            fclose($handle);
            throw $e;
        }
        fclose($handle);
    }

    private function getErrorMessage($prefix) {
        if ($this->isDefaultPath === false) {
            $prefix .= ", defined in 'hyperframework.logging.log_path'";
        }
        return $prefix . '.';
    }
}
