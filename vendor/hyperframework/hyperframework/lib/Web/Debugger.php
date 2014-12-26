<?php
namespace Hyperframework\Web;

use ErrorException;
use Hyperframework\Common\StackTraceFormatter;
use Hyperframework\Common\FileLoader;

class Debugger {
    private static $source;
    private static $headers;
    private static $headerCount;
    private static $content;
    private static $isError;
    private static $rootPath;
    private static $rootPathLength;

    public static function execute(
        $source, array $headers = null, $content = null
    ) {
        self::$source = $source;
        self::$headers = $headers;
        self::$content = $content;
        self::$headerCount = count($headers);
        self::$isError = $source instanceof ErrorException;
        if (headers_sent() === false) {
            header('Content-Type: text/html;charset=utf-8');
        }
        if (self::$isError) {
            if ($source->shouldThrow() === true) {
                $type = 'Error Exception';
            } elseif ($source->getSeverityAsString() === 'error') {
                $type = 'Fatal Error';
            } else {
                $type = ucwords($source->getSeverityAsString());
            }
        } else {
            $type = get_class($source);
        }
        $type = htmlspecialchars(
            $type, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
        );
        $message = (string)$source->getMessage();
        $title = $type;
        if ($message !== '') {
            $message = htmlspecialchars(
                $message, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
            );
            $title .= ' - ' . $message;
        }
        echo '<!DOCTYPE html><html><head><title>', $title, '</title>';
        self::renderCss();
        echo '</head><body>';
        self::renderHeader($type, $message);
        self::renderNav();
        self::renderContent();
        self::renderJs();
        echo '</body></html>';
    }

    private static function renderContent() {
        echo '<div id="content">';
        self::renderStatusBar();
        echo '<div id="file"><h2>File <span class="path">';
        self::renderPath(self::$source->getFile());
        echo '</span></h2>';
        $lines = self::getLines();
        $errorLineNumber = self::$source->getLine();
        foreach ($lines as $number => $line) {
            echo '<div';
            if ($number === $errorLineNumber) {
                echo ' class="error-line-number"';
            }
            echo '>', $number, '</div>';
        }
        echo '<table><tr><td>';
        foreach ($lines as $number => $line) {
            echo '<div';
            if ($number === $errorLineNumber) {
                echo ' class="error-line"';
            }
            echo '>', $line, '</div>';
        }
        echo '</td><td>';
        echo '</td></tr></table></div>';
        if (self::$isError === false || self::$source->isFatal() === false) {
            echo '<div id="stack-trace"><h2>Stack Trace</h2><div>';
            echo '</div></div>';
        }
        echo '</div>';
    }

    private static function renderStatusBar() {
        echo '<div id="status-bar"><div>Response Headers: <span>',
            self::$headerCount, '</span> ',
            'Content Length: <span>', strlen(self::$content),
            '</span></div><div>App Root Path: <span>';
            self::renderPath(FileLoader::getDefaultRootPath(), false);
        echo '</span><div></div>';
    }

    private static function getLines() {
    }

    private static function renderPath($path, $shouldRemoveRootPath = true) {
        if ($shouldRemoveRootPath) {
            if (self::$rootPath === null) {
                self::$rootPath = FileLoader::getDefaultRootPath()
                    . DIRECTORY_SEPARATOR;
                self::$rootPathLength = strlen(self::$rootPath);
            }
            if (strncmp(self::$rootPath, $path, self::$rootPathLength) === 0) {
                $path = substr($path, self::$rootPathLength);
            }
        }
        echo str_replace(
            DIRECTORY_SEPARATOR,
            '<span class="separator">' . DIRECTORY_SEPARATOR . '</span>', $path 
        );
    }

    private static function renderHeader($type, $message) {
        echo '<div id="header"><h1>', $type, '</h1><div class="message">',
            $message, '</div></div>';
    }

    private static function renderNav() {
        echo '<div id="nav"><div class="selected">Code</div>',
           '<div>Output</div></div>';
    }

    private static function renderJs() {
        echo <<<JAVASCRIPT
JAVASCRIPT;
    }

    private static function renderCss() {
        echo <<<CSS
CSS;
    }
}
