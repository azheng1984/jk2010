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
        echo '</span></div></div>';
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
                if ($value === '"') {
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
            $content = str_replace(["\r\n", "\r"], "\n", $content);
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
            '<span class="separator">' . DIRECTORY_SEPARATOR . '</span>',
            $path 
        );
    }

    private static function renderHeader($type, $message) {
        echo '<div id="header"><h1>', $type, '</h1><div id="message">',
            $message, '</div>',
            '<div id="nav"><div class="selected" id="nav-code"><span>',
            'Code</span></div><div id="nav-output"><a>Output</a></div></div></div>';
    }

    private static function renderJavascript() {
        $isOverflow = false;
        $hiddenContent = null;
        $headers = [];
        if (self::$headers !== null) {
            foreach (self::$headers as $header) {
                $segments = explode(':', $header, 2);
                $key = $segments[0];
                if (isset($segments[1])) {
                    $value = ltrim(htmlspecialchars(
                        $segments[1],
                        ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
                    ), ' ');
                } else {
                    $value = '';
                }
                $key = htmlspecialchars(
                    $key, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
                );
                $headers[] = [$key, $value];
            }
        }
        $outputLimitation = 10 * 1024 * 1024;
        if (self::$contentLength >= $outputLimitation) {
            $isOverflow = true;
            $content = mb_strcut(self::$content, 0, $outputLimitation);
        } else {
            $content = self::$content;
        }
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $maxInitContentLength = 1;//256 * 1024;
        if (self::$contentLength > $maxInitContentLength) {
            $tmp = $content;
            $content = mb_strcut($tmp, 0, $maxInitContentLength);
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
    document.getElementById("nav-code").innerHTML = '<a href="javascript:showCode()">Code</a>';
    document.getElementById("nav-code").className = '';
    document.getElementById("nav-output").innerHTML = '<span>Output</span>';
    document.getElementById("nav-output").className = 'selected';
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
            + '<a id="show-response-headers-botton"'
            + ' href="javascript:toggleResponseHeaders()">'
            + '<span id="arrow">►</span> Headers <span>' + headers.length
            + '</span></a><table id="response-headers-content"><tbody>';
        for (var index = 0; index < headers.length; ++index) {
            var header = headers[index];
            outputContent += '<tr><td>' + header[0]
                + ':</td><td>' + header[1] + '</td></tr>';
        }
        outputContent += '</tbody></table></div>';
    }
    if (isOverflow) {
        outputContent += '<div class="notice">Notice: Content is partial,'
            + ' length is larger then output limitation(10MB).</div>';
    }
    var responseBodyHtml = '<table id="response-body">'
        + '<tbody id="response-body-content">'
        + buildOutputContent(content) + '</tbody></table>';
    if (hiddenContent != null) {
        fullContent = content + hiddenContent;
        var buttonName = "Show all content";
        var href = "javascript:showAllContent()";
        if (isOverflow) {
            buttonName = "Show more content";
            href = "javascript:showMoreContent()";
        }
        responseBodyHtml = '<a id="show-hidden-content-button-top"'
            + ' href="' + href + '">' + buttonName + '</a>'
            + responseBodyHtml
            + '<a id="show-hidden-content-button-bottom"'
            + ' href="' + href + '">' + buttonName + '</a>';
    }
    codeContent = contentDiv.innerHTML;
    contentDiv.innerHTML = outputContent + responseBodyHtml;
}

function showAllContent() {
    showHiddenContent();
}

function showMoreContent() {
    showHiddenContent();
}

function showHiddenContent() {
    document.getElementById("show-hidden-content-button-top").style.display
        = 'none';
    document.getElementById("show-hidden-content-button-bottom").style.display
        = 'none';
    document.getElementById("response-body-content").innerHTML
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
    document.getElementById("nav-code").innerHTML = '<span>Code</span>';
    document.getElementById("nav-code").className = 'selected';
    document.getElementById("nav-output").innerHTML =
        '<a href="javascript:showOutput()">Output</a>';
    document.getElementById("nav-output").className = '';
    var contentDiv = document.getElementById("content");
    outputContent = contentDiv.innerHTML;
    contentDiv.innerHTML = codeContent;
    codeContent = null;
}

function toggleResponseHeaders() {
    var div = document.getElementById("response-headers-content");
    if (div.style.display == "none") {
        document.getElementById("arrow").innerHTML = '▼';
        div.style.display = "block";
    } else {
        document.getElementById("arrow").innerHTML = '►';
        div.style.display = "none";
    }
}

document.getElementById("nav-output").innerHTML =
    '<a href="javascript:showOutput()">Output</a>';
</script>
<?php
    }

    private static function renderCss() {
?>
<style>
body {
    font-family: Helvetica, Arial, sans-serif;
    font-size: 13px;
    background: #eee;
}
h1, h2, table {
    width: 100%;
}
div, h1, h2, table {
    float: left;
}
a {
    text-decoration: none;
    color: #333;
}
a:hover {
    color: #09d;
}
h1, h2, body {
    margin: 0;
}
h1, #message {
    color: #fff;
    font-weight: normal;
    font-size: 22px;
    background: #c22;
    text-shadow: 1px 1px 0 rgba(0, 0, 0, .4);
    padding: 10px;
}
#header {
    width: 100%;
}
#message {
    width: 100%;
    font-size:16px;
    padding-top: 0;
    line-height: 20px;
}
#nav {
    width: 100%;
    padding: 0 10px;
    border-bottom: 1px solid #ccc;
    font-size: 14px;
    background: #f8f8f8;
    padding-top: 10px;
    font-weight: bold;
    position: relative;
    height: 28px;
}
#nav div {
    line-height: 16px;
    width: auto;
    padding: 5px 25px;
    border: 1px solid #f8f8f8;
}
#nav div.selected {
    border-color: #ccc;
    border-bottom: 1px solid #eee;
    background: #eee;
    padding: 5px 25px 6px;
    border-radius: 2px 2px 0 0;
}
</style>
<?php
    }
}
