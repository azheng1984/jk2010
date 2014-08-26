<?php
namespace Hyperframework;

use Exception;

class WebClient {
    private static $isOldCurl;
    private $handle;
    private $options = array();
    private $temporaryOptions;
    private $stdStreams;
    private $isInFileOptionDirty;

    public function __construct() {
        if (self::$isOldCurl === null) {
            self::$isOldCurl = version_compare(phpversion(), '5.5.0', '<');
        }
        $this->handle = curl_init();
        $defaultOptions = $this->getDefaultOptions();
        if ($defaultOptions !== null) {
            $this->setOptions($defaultOptions);
        }
    }

    protected function getDefaultOptions() {
        return array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_ENCODING => '',
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 1
        );
    }

    public function setOptions($options) {
        curl_setopt_array($this->handle, $options);
        foreach ($options as $name => $value) {
            if ($value !== null) {
                $this->options[$name] = $value;
            } else {
                unset($this->options[$name]);
            }
            if ($this->temporaryOptions !== null) {
                unset($this->temporaryOptions[$name]);
            }
        }
    }

    public function setOption($name, $value) {
        $this->setOptions(array($name => $value));
    }

    protected function send($method, $url, $options) {
        if ($this->temporaryOptions !== null) {
            foreach ($this->temporaryOptions as $name => $value) {
                if ($options !== null && array_key_exists($options, $name)) {
                    continue;
                }
                if (isset($this->options[$name])) {
                    curl_setopt($handle, $name, $this->options[$name]);
                } else {
                    if ($name === CURLOPT_HTTPHEADER
                        || $name === CURLOPT_POSTQUOTE
                        || $name === CURLOPT_QUOTE
                        || (defined('CURLOPT_HTTP200ALIASES')
                            && $name === CURLOPT_HTTP200ALIASES)
                    ) {
                        curl_setopt($handle, $name, array());
                        continue;
                    }
                    if (self::$isOldCurl === false) {
                        curl_setopt($this->handle, $name, null);
                        continue;
                    }
                    if ($name === CURLOPT_FILE || CURLOPT_WRITEHEADER) {
                        curl_setopt(
                            $this->handle, $name, $this->getStdStream()
                        );
                    } elseif ($name === CURLOPT_STDERR) {
                        curl_setopt(
                            $this->handle, $name, $this->getStdStream(true)
                        );
                    } elseif ($name === CURLOPT_INFILE) {
                        $this->isInFileOptionDirty = true;
                    } else {
                        curl_setopt($this->handle, $name, null);
                    }
                }
            }
        }
        if ($options !== null) {
            curl_setopt_array($this->handle, $options);
        }
        $this->temporaryOptions = $options;
        if (self::$isOldCurl && $this->isInFileOptionDirty) {
            if (array_key_exists($options, CURLOPT_INFILE)
                || array_key_exists($this->options, CURLOPT_INFILE)
            ) {
                $this->isInFileOptionDirty = false;
                $readCallback = $this->getReadCallback();
                if ($readCallback === null) {
                    curl_setopt($this->handle, CURLOPT_READFUNCTION, null);
                } else {
                    curl_setopt(
                        $this->handle, CURLOPT_READFUNCTION, $readCallback
                    );
                }
            } else {
                $this->addReadWrapper();
            }
        }
        curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->handle, CURLOPT_URL, $url);
        curl_exec($this->handle);
    }

    private function addReadWrapper() {
        $callback = null;
        $readCallback = $this->getReadCallback();
        if ($readCallback !== null) {
            $callback = function($handle, $dirtyHandle, $maxLength)
                use ($readCallback)
            {
                return call_user_func($readCallback, $handle, null, $maxLength);
            }
        } else {
            $callback = function() {
                throw new Exception;
            };
        }
        curl_setopt($this->handle, CURLOPT_READFUNCTION, $callback);
    }

    private function getReadCallback() {
        $readCallback = null;
        if ($this->temporaryOptions !== null && array_key_exists(
            $this->temporaryOptions, CURLOPT_READFUNCTION
        )) {
            $readCallback =
                $this->temporaryOptions[CURLOPT_READFUNCTION];
        } elseif (isset($this->options[CURLOPT_READFUNCTION])) {
            $readCallback = $this->options[CURLOPT_READFUNCTION];
        }
        return $realCallback;
    }

    private function getStdSteam($isError = false) {
        if (PHP_SAPI === 'cli' ) {
            if ($isError) {
                return STDERR;
            }
            return STDOUT;
        }
        if ($this->stdSteams === null) {
            $this->stdSteams = array();
        }
        if ($isError) {
            if (isset($this->stdSteams['error']) === false) {
                $this->stdSteams['error'] = fopen('php://stderr', 'w');
            }
            return $this->stdSteams['error'];
        }
        if (isset($this->stdSteams['output']) === false) {
            $this->stdSteams['output'] = fopen('php://output', 'w');
        }
        return $this->stdSteams['output'];
    }

    public funciton getInfo($name = 0) {
        return curl_getinfo($this->handle, $name);
    }

    public function pause($bitmask) {
        if (self::$isOldCurl) {
            throw new Exception;
        }
        $result = curl_pause($this->handle, $bitmast);
        if ($result !== CURLE_OK) {
            throw new Exception;
        }
    }

    public function reset() {
        if (self::$isOldCurl) {
            throw new Exception;
        }
        curl_reset($this->handle);
    }

    public function close() {
        curl_close($this->handle);
        $this->handle = null;
    }

    public function __destruct() {
        if ($this->handle !== null) {
            $this->close();
        }
    }

    public function __clone() {
        $this->handle = curl_copy_handle(self::$handle);
    }

    public static function sendAll($requests) {
    }

    public function head($url, $options = null) {
        self::send('HEAD', $url, $options);
    }

    public function get($url, $options = null) {
        self::send('GET', $url, $options);
    }

    public function post($url, $options = null) {
        self::send('POST', $url, $options);
    }

    public function patch($url, $options = null) {
        self::send('PATCH', $url, $options);
    }

    public function put($url, $options = null) {
        self::send('PUT', $url, $options);
    }

    public function delete($url, $options = null) {
        self::send('DELETE', $url, $options);
    }

    public function options($url, $options = null) {
        self::send('OPTIONS', $url, $options);
    }

    public function trace($url, $options = null) {
        self::send('TRACE', $url, $options);
    }
}
