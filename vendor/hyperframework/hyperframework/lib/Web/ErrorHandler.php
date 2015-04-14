<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ErrorHandler as Base;

class ErrorHandler extends Base {
    private $isDebuggerEnabled;
    private $startupOutputBufferLevel;

    public function __construct() {
        $this->isDebuggerEnabled =
            Config::getBool('hyperframework.web.debugger.enable', false);
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
        if ($this->isDebuggerEnabled) {
            $this->flushInnerOutputBuffer();
            $outputBuffer = $this->getOutputBuffer();
            $this->deleteOutputBuffer();
            if (Response::headersSent() === false) {
                $this->rewriteHttpHeaders();
            }
            $this->executeDebugger($outputBuffer);
            ini_set('display_errors', '0');
        } elseif (Response::headersSent() === false) {
            $this->rewriteHttpHeaders();
            $this->deleteOutputBuffer();
            $this->renderErrorView();
        }
    }

    /**
     * @return string
     */
    protected function getOutputBuffer() {
        $content = ob_get_contents();
        if ($content === false) {
            return;
        }
        return $content;
    }

    /**
     * @param string $outputBuffer
     */
    protected function executeDebugger($outputBuffer) {
        $configName = 'hyperframework.web.debugger.class';
        $class = Config::getString($configName, '');
        if ($class === '') {
            $debugger = new Debugger;
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Class '$logHandlerClass' does not exist,"
                        . " set using config '$configName'."
                );
            }
            $debugger = new $class;
        }
        $debugger->execute($this->getError(), $outputBuffer);
    }

    protected function renderErrorView() {
        $configName = 'hyperframework.web.error_view.class';
        $class = Config::getString($configName, '');
        if ($class === '') {
            $view = new ErrorView;
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Class '$class' does not exist, set "
                        . "using config '$configName'."
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
            $shouldLogHttpException = Config::getBool(
                'hyperframework.web.log_http_exception', false
            );
            if ($shouldLogHttpException === false) {
                return;
            }
        }
        parent::writeLog();
    }

    private function flushInnerOutputBuffer() {
        $level = ob_get_level();
        $startupLevel = $this->startupOutputBufferLevel;
        if ($level < $startupLevel) {
            return;
        }
        while ($level > $startupLevel) {
            ob_end_flush();
            --$level;
        }
    }

    private function deleteOutputBuffer() {
        $level = ob_get_level();
        $startupLevel = $this->startupOutputBufferLevel;
        while ($level >= $startupLevel) {
            if ($startupLevel === $level) {
                if ($level !== 0) {
                    ob_clean();
                }
            } else {
                ob_end_clean();
            }
            --$level;
        }
    }

    private function rewriteHttpHeaders() {
        Response::removeHeaders();
        $error = $this->getError();
        if ($error instanceof HttpException) {
            foreach ($error->getHttpHeaders() as $header) {
                Response::setHeader($header);
            }
        } else {
            Response::setHeader('HTTP/1.1 500 Internal Server Error');
        }
    }
}
