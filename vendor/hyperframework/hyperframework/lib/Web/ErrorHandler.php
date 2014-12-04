<?php
namespace Hyperframework\Web;

use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Web\Html\Debugger;
use Hyperframework\Common\ErrorHandler as Base;

class ErrorHandler extends Base {
    private static $isDebuggerEnabled;
    private static $startupOutputBufferLevel;

    public static function run() {
        self::$isDebuggerEnabled =
            Config::get('hyperframework.error_handler.debug');
        if (ini_get('display_errors') === '1') {
            if (self::$isDebuggerEnabled !== false) {
                self::$isDebuggerEnabled = true;
            }
        } else {
            if (self::$isDebuggerEnabled !== true) {
                self::$isDebuggerEnabled = false;
            }
        }
        if (self::$isDebuggerEnabled) {
            ob_start();
        }
        self::$startupOutputBufferLevel = ob_get_level();
        parent::run();
    }

    protected static function displayFatalError() {
        $isError = static::isError();
        $source = static::getSource();
        if (self::$isDebuggerEnabled) {
            $headers = headers_list();
            if (headers_sent() === false) {
                self::resetHttpHeaders();
            }
            $outputBuffer = static::getOutputBuffer();
            static::executeDebugger($headers, $outputBuffer);
        } elseif (ini_get('display_errors') === '1') {
            static::displayError();
        } elseif (headers_sent() === false) {
            self::resetHttpHeaders();
            self::deleteOutputBuffer();
            static::renderErrorView();
        }
    }

    private static function resetHttpHeaders() {
        header_remove();
        $source = self::getSource();
        if ($source instanceof HttpException) {
            foreach ($source->getHttpHeaders() as $header) {
                header($header);
            }
        } else {
            header('HTTP/1.1 500 Internal Server Error');
        }
    }

    private static function deleteOutputBuffer() {
        if (self::$startupOutputBufferLevel === null) {
            throw new Exception;
        }
        $obLevel = ob_get_level();
        while ($obLevel > self::$startupOutputBufferLevel) {
            ob_end_clean();
            --$obLevel;
        }
    }

    protected static function getOutputBuffer() {
        if (self::$startupOutputBufferLevel === null) {
            throw new Exception;
        }
        $outputBufferLevel = ob_get_level();
        if ($outputBufferLevel < self::$startupOutputBufferLevel) {
            return;
        }
        while ($outputBufferLevel > self::$startupOutputBufferLevel) {
            ob_end_flush();
            --$outputBufferLevel;
        }
        $content = ob_get_contents();
        ob_end_clean();
        if ($content === '') {
            return;
        }
        //config
        $charset = null;
        $encoding = null;
        foreach (headers_list() as $header) {
            $header = str_replace(' ', '', strtolower($header));
            if ($header === 'content-encoding:gzip') {
                $encoding = 'gzip';
            } elseif ($header === 'content-encoding:deflate') {
                $encoding = 'deflate';
            } elseif (strncmp('content-type:', $header, 13) === 0) {
                $header = substr($header, 13);
                $segments = explode(';', $header);
                foreach ($segments as $segment) {
                    if (strncmp('charset=', $segment, 8) === 0) {
                        $charset = substr($segment, 8);
                        break;
                    }
                }
            }
        }
        if ($encoding !== null) {
            $content = static::decodeOutputBuffer($content, $encoding);
        } 
        if ($charset !== null) {
            $content = static::convertOutputBufferCharset($content, $charset);
        }
        return $content;
    }

    private static function decodeOutputBuffer($content, $encoding) {
        if ($encoding === 'gzip') {
            $result = file_get_contents(
                'compress.zlib://data:;base64,' . base64_encode($content)
            );
            if ($result !== false) {
                $content = $result;
            }
        } elseif ($encoding === 'deflate') {
            $result = gzinflate($content);
            if ($result !== false) {
                $content = $result;
            }
        }
        return $content;
    }

    private static function convertOutputBufferCharset($content, $charset) {
        //config, allow gbk
        if ($charset !== 'utf-8') {
            $result = iconv($charset, 'utf-8', $content);
            if ($result !== false) {
                $content = $result;
            }
        }
        return $content;
    }

    protected static function executeDebugger($headers, $outputBuffer) {
        Debugger::execute(
            static::getSource(), static::getPreviousErrors(),
            $headers, $outputBuffer
        );
    }

    protected static function renderErrorView() {
        $type = null;
        if (self::isError() === true) {
            $type = 'error';
        } else {
            $type = 'exception';
        }
        $template = new ViewTemplate(
            ['source' => self::getSource(), 'type' => $type]
        );
        $format = static::getErrorViewFormat();
        $prefix = $template->getRootPath() . DIRECTORY_SEPARATOR
            . '_error' . DIRECTORY_SEPARATOR . 'show.';
        if ($format !== null && $format !== 'php') {
            if (file_exists($prefix . $format . '.php')) {
                $template->load('_error/show.' . $format . '.php');
                return;
            }
        }
        if (file_exists($prefix . 'php')) {
            $template->load('_error/show.php');
            return;
        }
        header('Content-Type:text/plain; charset=utf-8');
        if (self::getSource() instanceof HttpException) {
            echo self::getSource()->getCode();
        } else {
            echo '500 Internal Server Error';
        }
    }

    protected static function getErrorViewFormat() {
        $pattern = '#\.([0-9a-zA-Z]+)$#';
        $requestPath = RequestPath::get();
        if (preg_match($pattern, $requestPath, $matches) === 1) {
            return $matches[1];
        }
    }

    final protected static function isDebuggerEnabled() {
        return self::$isDebuggerEnabled;
    }

    final protected static function enableDebugger() {
        self::$isDebuggerEnabled = true;
    }

    final protected static function disableDebugger() {
        self::$isDebuggerEnabled = false;
    }

    final protected static function getStartupOutputBufferLevel() {
        return self::$startupOutputBufferLevel;
    }
}
