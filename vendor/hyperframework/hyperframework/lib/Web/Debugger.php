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
    private static $contentLength;
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
        self::$contentLength = strlen($content);
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
        echo '<table><tbody><tr><td>';
        $lines = self::getLines();
        $errorLineNumber = self::$source->getLine();
        foreach ($lines as $number => $line) {
            echo '<div';
            if ($number === $errorLineNumber) {
                echo ' class="error-line-number"';
            }
            echo '>', $number, '</div>';
        }
        echo '</td><td>';
        foreach ($lines as $number => $line) {
            echo '<div';
            if ($number === $errorLineNumber) {
                echo ' class="error-line"';
            }
            echo '>', $line, '</div>';
        }
        echo '</td></tr></tbody></table></div>';
        if (self::$isError === false || self::$source->isFatal() === false) {
            echo '<div id="stack-trace"><h2>Stack Trace</h2>',
                '<div><table><tbody>';
            if (self::$isError) {
                $trace =  self::$source->getSourceTrace();
            } else {
                $trace =  self::$source->getTrace();
            }
            $index = 0;
            foreach ($trace as $frame) {
                if ($frame !== '{main}') {
                    $invocation = StackTraceFormatter::formatInvocation($frame);
                    echo '<tr><td>', $index,
                        '</td><td><div class="invocation">', $invocation,
                        '</div>';
                    echo '<div class="position">';
                    if (isset($frame['file'])) {
                        self::renderPath($frame['file']);
                        echo ' <span class="line">', $frame['line'], '</span>';
                    } else {
                        echo  'internal function';
                    }
                    echo '</div>';
                    echo  '</td></tr>';
                }
                ++$index;
            }
            echo '</tbody></table></div></div>';
        }
        echo '</div>';
    }

    private static function renderStatusBar() {
        echo '<div id="status-bar"><div>Response Headers: <span>',
            self::$headerCount, '</span> ',
            'Content Length: <span>', self::$contentLength,
            '</span></div><div>App Root Path: <span>';
            self::renderPath(FileLoader::getDefaultRootPath(), false);
        echo '</span><div></div>';
    }

    private static function getLines() {
        $file = file_get_contents(self::$source->getFile());
        $tokens = token_get_all($file);
        $errorLineNumber = self::$source->getLine();
        $firstLineNumber = 0;
        if ($errorLineNumber > 21) {
            $firstLineNumber = $errorLineNumber - 21;
        }
        $lineNumber = 0;
        $result = [];
        $buffer = '';
        foreach ($tokens as $index => $value) {
            if (is_string($value)) {
                if ($lineNumber < $firstLineNumber) {
                    continue;
                }
                if ($value === '"' || $value === "'") {
                    $buffer .= '<span class="string">' . $value . '</span>';
                } else {
                    $buffer .= '<span class="keyword">' . $value . '</span>';
                }
                continue;
            }
            if ($value[2] < $firstLineNumber) {
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
                if ($lineNumber >= $firstLineNumber) {
                    $result[$lineNumber] =
                        $buffer . self::formatToken($type, $line);
                    $buffer = '';
                    ++$lineNumber;
                }
            }
            $buffer .= self::formatToken($type, $lastLine);
            if ($lineNumber > $errorLineNumber + 10) {
                $buffer = false;
                break;
            }
        }
        if ($buffer !== false) {
            $result[$lineNumber] = $buffer;
        }
        $count = count($result);
        if ($count > 21) {
            $first = key($result) + $count - 21;
            for ($index = key($result); $index < $first; ++$index) {
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
        echo '<div id="nav"><div onclick="showCode()" class="selected">Code</div>',
           '<div onclick="showOutput()">Output</div></div>';
    }

    private static function renderJavascript() {
        $isOverflow = false;
        $hiddenContent = null;
        $headers = [];
        if (self::$headers !== null) {
            foreach (self::$headers as $header) {
                list($key, $value) = explode(':', $header, 2);
                $key = htmlspecialchars(
                    $key, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
                );
                $value = ltrim(htmlspecialchars(
                    $value , ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
                ), ' ');
                $headers[] = [$key, $value];
            }
        }
        if (self::$contentLength >= 10 * 1024 * 1024) {
            $isOverflow = true;
            $content = mb_strcut($buffer, 0, 10 * 1024 * 1024);
        } else {
            $content = self::$content;
        }
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $maxContentLength = 256 * 1024;
        if (self::$contentLength > $maxContentLength) {
            $tmp = $content;
            $content = mb_strcut($tmp, 0, $maxContentLength);
            $hiddenContent = substr($tmp, strlen($content));
        }
        $content = json_encode(htmlspecialchars(
            $content, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
        ));
        if ($hiddenContent !== null) {
            $hiddenContent = json_encode(htmlspecialchars(
                $hiddenContent, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
            ));
        } else {
            $hiddenContent = 'null';
        }
?>
<script type="text/javascript">
var codeContent = null;
var outputContent = null;
var fullContent = null;
function showOutput() {
    if (codeContent != null) {
        return;
    }
    var contentDiv = document.getElementById("content");
    if (outputContent != null) {
        codeContent = contentDiv.innerHTML;
        contentDiv.innerHTML = outputContent;
        outputContent = null;
        return;
    }
    var headers = <?= json_encode($headers) ?>;
    var isOverflow = <?= json_encode($isOverflow) ?>;
    var contentLength = <?= json_encode(self::$contentLength) ?>;
    var content = <?= $content ?>;
    var hiddenContent = <?= $hiddenContent ?>;
    if (headers.length > 0) {
        outputContent = '<div id="response-headers">'
            + '<div id="show-headers-botton" onclick="toggleResponseHeaders()">'
            + '<span id="arrow">►</span> Headers <span>' + headers.length
            + '</span></div><table id="response-headers-content"><tbody>';
        for (var index = 0; index < headers.length; ++index) {
            var header = headers[index];
            outputContent += '<tr><td>' + header[0]
                + '</td><td>' + header[1] + '</td></tr>';
            outputContent += '</tbody></table>';
        }
        outputContent += '</div>';
    }
    if (isOverflow) {
        outputContent += '<div>overflow</div>';
    }
    var responseBodyHtml = '<table id="response-body"><tbody>'
        + buildOutputContent(content) + '</tbody></table>';
    if (hiddenContent != null) {
        fullContent = content + hiddenContent;
        responseBodyHtml = '<div id="top-show-hidden-content-button-top"'
            + ' onclick="showHiddenContent()">Show hidden content</div>'
            + responseBodyHtml
            + '<div id="top-show-hidden-content-button-bottom"'
            + ' onclick="showHiddenContent()">Show hidden content</div>';
    }
    codeContent = contentDiv.innerHTML;
    contentDiv.innerHTML = outputContent + responseBodyHtml;
}

function showHiddenContent() {
    document.getElementById("show-hidden-content-button-top") .style.display
        = 'none';
    document.getElementById("show-hidden-content-button-bottom") .style.display
        = 'none';
    document.getElementById("response-body").childNode.innerHTML
        = buildOutputContent(fullContent);
    fullContent = null;
}

function buildOutputContent(content) {
    var result = '';
    var lines = content.split("\n");
    var count = lines.length;
    for (var index = 0; index < count; ++index) {
        result += '<tr><td line-number="'
            + (index + 1) + '"></td><td>' + lines[index] + '</td></tr>';
    }
    return result;
}

function showCode() {
    if (codeContent == null) {
        return;
    }
    var contentDiv = document.getElementById("content");
    outputContent = contentDiv.innerHTML;
    contentDiv.innerHTML = codeContent;
    codeContent = null;
}

function toggleResponseHeaders() {
    var div = document.getElementById("response-headers-content");
    if (div.style.display == 'none') {
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
}
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
