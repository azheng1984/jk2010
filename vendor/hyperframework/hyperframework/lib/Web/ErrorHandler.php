<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ErrorHandler as Base;

class ErrorHandler extends Base {
    private $isDebuggerEnabled;
    private $startupOutputBufferLevel;

    public function __construct() {
        $this->isDebuggerEnabled =
            Config::getBoolean('hyperframework.error_handler.debug', false);
        if ($this->isDebuggerEnabled) {
            ob_start();
        }
        $this->startupOutputBufferLevel = ob_get_level();
    }

    protected function handle() {
        $this->writeLog();
        $this->displayError();
    }

    protected function displayError() {
        $error = $this->getError();
        if ($this->isDebuggerEnabled()) {
            $outputBuffer = $this->getOutputBuffer();
            $headers = ResponseHeaderHelper::getHeaders();
            if (ResponseHeaderHelper::isSent() === false) {
                $this->rewriteHttpHeaders();
            }
            $this->executeDebugger($headers, $outputBuffer);
            ini_set('display_errors', '0');
        } elseif (ResponseHeaderHelper::isSent() === false) {
            $this->rewriteHttpHeaders();
            $this->deleteOutputBuffer();
            $this->renderErrorView();
        }
    }

    protected function getOutputBuffer() {
        $outputBufferLevel = ob_get_level();
        $startupOutputBufferLevel = $this->getStartupOutputBufferLevel();
        if ($outputBufferLevel < $startupOutputBufferLevel) {
            return;
        }
        while ($outputBufferLevel > $startupOutputBufferLevel) {
            ob_end_flush();
            --$outputBufferLevel;
        }
        $content = ob_get_contents();
        if ($content === false) {
            return $content;
        }
        ob_clean();
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

    protected function isDebuggerEnabled() {
        return $this->isDebuggerEnabled;
    }

    protected function getStartupOutputBufferLevel() {
        return $this->startupOutputBufferLevel;
    }

    private function deleteOutputBuffer() {
        $obLevel = ob_get_level();
        $startupOutputBufferLevel = $this->getStartupOutputBufferLevel();
        while ($obLevel >= $startupOutputBufferLevel) {
            if ($startupOutputBufferLevel === $obLevel) {
                ob_clean();
            } else {
                ob_end_clean();
            }
            --$obLevel;
        }
    }

    private function rewriteHttpHeaders() {
        ResponseHeaderHelper::removeAllHeaders();
        $error = $this->getError();
        if ($error instanceof HttpException) {
            foreach ($error->getHttpHeaders() as $header) {
                ResponseHeaderHelper::setHeader($header);
            }
        } else {
            ResponseHeaderHelper::setHeader(
                'HTTP/1.1 500 Internal Server Error'
            );
        }
    }
}
