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
        self::renderJavascript();
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
        $file = file_get_contents(self::$source->getFile());
        $errorLineNumber = self::$source->getLine();
        $startingLineNumber = 0;
        if ($errorLineNumber > 21) {
            $startingLineNumber = $errorLineNumber - 21;
        }
        $tokens = token_get_all($file);
        $lineNumber = 0;
        $result = [];
        $buffer = '';
        $isString = false;
        foreach ($tokens as $index => $value) {
            if (is_string($value)) {
                if ($lineNumber < $startingLineNumber) {
                    continue;
                }
                if ($value === '"' || $value === "'") {
                    $buffer .= '<span class="string">' . $value . '</span>';
                } else {
                    $buffer .= '<span class="keyword">' . $value . '</span>';
                }
                continue;
            }
            if ($value[2] < $startingLineNumber) {
                continue;
            }
            $lineNumber = $value[2];
            $type = $value[0];
            $content = $value[1];
            $content = str_replace("\r\n", "\n", $content);
            $content = str_replace("\r", "\n", $content);
            $lines = explode("\n", $content);
            $lastLine = array_pop($lines);
            foreach ($lines as $line) {
                if ($lineNumber >= $startingLineNumber) {
                    $result[$lineNumber] =
                        $buffer . self::formatToken($type, $line);
                    $buffer = '';
                    ++$lineNumber;
                }
            }
            $buffer .= self::formatToken($type, $lastLine);
            if ($lineNumber > $errorLineNumber + 10) {
                var_dump($lineNumber);
                $buffer = false;
                break;
            }
        }
        if ($buffer !== false) {
            $result[$lineNumber] = $buffer;
        }
        $count = count($result);
        if ($count > 21) {
            $startingPoint = key($result) + $count - 21;
            for ($index = key($result); $index < $startingPoint; ++$index) {
                unset($result[$index]);
            }
        }
        return $result;
    }

    private static function formatToken($type, $content) {
        switch ($type) {
            case T_ENCAPSED_AND_WHITESPACE:
            case T_CONSTANT_ENCAPSED_STRING:
                $class = 'string';
                break;
            case T_WHITESPACE:
            case T_STRING:
            case T_NUM_STRING:
            case T_VARIABLE:
            case T_DNUMBER:
            case T_LNUMBER:
            case T_HALT_COMPILER:
            case T_EVAL:
            case T_CURLY_OPEN:
            case T_UNSET:
            case T_STRING_VARNAME:
            case T_PRINT:
            case T_REQUIRE:
            case T_REQUIRE_ONCE:
            case T_INCLUDE:
            case T_INCLUDE_ONCE:
            case T_ISSET:
            case T_LIST:
            case T_CLOSE_TAG:
            case T_OPEN_TAG:
            case T_OPEN_TAG_WITH_ECHO:
                $class = 'default';
                break;
            case T_COMMENT:
            case T_DOC_COMMENT:
                $class = 'comment';
                break;
            case T_INLINE_HTML:
                $class = 'html';
                break;
            default:
                $class = 'keyword';
                //$class = token_name($type);
        }
        if ($class === 'default') {
            return $content; 
        }
        return '<span class="' . $class . '">' . $content . '</span>';
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

    private static function renderJavascript() {
?>
<script type="text/javascript">
    var x = 'hello';
</script>
<?php
    }

    private static function renderCss() {
?>
<style>
body {
    background: #ccc;
}
</style>
<?php
    }
}
