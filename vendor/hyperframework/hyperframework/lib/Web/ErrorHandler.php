<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ErrorHandler as Base;

class ErrorHandler extends Base {
    private $isDebuggerEnabled;
    private $startupOutputBufferLevel;

    public function __construct() {
        parent::__construct();
        $this->isDebuggerEnabled =
            Config::getBoolean('hyperframework.error_handler.debug', false);
        if (ini_get('display_errors') === '1') {
            if ($this->isDebuggerEnabled !== false) {
                $this->isDebuggerEnabled = true;
            }
        } else {
            if ($this->isDebuggerEnabled !== true) {
                $this->isDebuggerEnabled = false;
            }
        }
        if ($this->isDebuggerEnabled) {
            ob_start();
        }
        $this->startupOutputBufferLevel = ob_get_level();
    }

    protected function displayFatalError() {
        $error = $this->getError();
        if ($this->isDebuggerEnabled) {
            $headers = headers_list();
            if (headers_sent() === false) {
                $this->rewriteHttpHeaders();
            }
            $outputBuffer = $this->getOutputBuffer();
            $this->executeDebugger($headers, $outputBuffer);
        } elseif (ini_get('display_errors') === '1') {
            $this->displayError();
        } elseif (headers_sent() === false) {
            $this->rewriteHttpHeaders();
            $this->deleteOutputBuffer();
            $this->renderErrorView();
        }
    }

    private function rewriteHttpHeaders() {
        header_remove();
        $error = $this->getError();
        if ($error instanceof HttpException) {
            foreach ($error->getHttpHeaders() as $header) {
                header($header);
            }
        } else {
            header('HTTP/1.1 500 Internal Server Error');
        }
    }

    protected function writeLog() {
        if ($this->getError() instanceof HttpException) {
            $shouldLogHttpException = Config::getBoolean(
                'hyperframework.error_handler.log_http_exception', false
            );
            if ($shouldLogHttpException === false) {
                return;
            }
        }
        parent::writeLog();
    }

    private function deleteOutputBuffer() {
        $obLevel = ob_get_level();
        while ($obLevel >= $this->startupOutputBufferLevel) {
            ob_end_clean();
            --$obLevel;
        }
    }

    protected function getOutputBuffer() {
        $outputBufferLevel = ob_get_level();
        if ($outputBufferLevel < $this->startupOutputBufferLevel) {
            return false;
        }
        while ($outputBufferLevel > $this->startupOutputBufferLevel) {
            ob_end_flush();
            --$outputBufferLevel;
        }
        $content = ob_get_contents();
        if ($content === false) {
            return $content;
        }
        ob_end_clean();
        if ($content === '') {
            return $content;
        }
        $charset = Config::getString(
            'Hyperframework.error_handler.output_buffer_charset', ''
        );
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
                        if ($charset !== '')
                        $charset = substr($segment, 8);
                        break;
                    }
                }
            }
        }
        if ($encoding !== null) {
            $content = $this->decodeOutputBuffer($content, $encoding);
        }
        if ($charset !== '') {
            $content = $this->convertOutputBufferCharset($content, $charset);
        }
        return $content;
    }

    private function decodeOutputBuffer($content, $encoding) {
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

    private function convertOutputBufferCharset($content, $charset) {
        if ($charset !== 'utf-8') {
            $result = iconv($charset, 'utf-8', $content);
            if ($result !== false) {
                $content = $result;
            }
        }
        return $content;
    }

    protected function executeDebugger($headers, $outputBuffer) {
        $class = Config::getString(
            'Hyperframework.error_handler.debugger_class', ''
        );
        if ($class === '') {
            $debugger = new Debugger;
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Debugger class '$logHandlerClass' does not exist,"
                        . " set using config "
                        . "'hyperframework.error_handler.debugger_class'."
                );
            }
            $debugger = new $class;
        }
        $debugger->execute($this->getError(), $headers, $outputBuffer);
    }

    protected function renderErrorView() {
        $class = Config::getString('hyperframework.error_view.class', '');
        if ($class === '') {
            $view = new ErrorView;
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Error view class '$class' does not exist, set "
                        . "using config 'hyperframework.error_view.class'."
                );
            }
            $view = new $class;
        }
        $error = $this->getError();
        if ($error instanceof HttpException) {
            $statusCode = $error->getStatusCode();
            $statusText = $error->getStatusText();
        } else {
            $statusCode = 500;
            $statusText = 'Internal Server Error';
        }
        $view->render($statusCode, $statusText, $error);
    }

    final protected function isDebuggerEnabled() {
        return $this->isDebuggerEnabled;
    }

    final protected function getStartupOutputBufferLevel() {
        return $this->startupOutputBufferLevel;
    }
}
