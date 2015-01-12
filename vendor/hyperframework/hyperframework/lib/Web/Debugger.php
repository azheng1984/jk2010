<?php
namespace Hyperframework\Web;

use ErrorException;
use Hyperframework\Common\StackTraceFormatter;
use Hyperframework\Common\Config;
use Hyperframework\Common\ArgumentErrorException;
use Hyperframework\Common\ConfigException;

class Debugger {
    private static $source;
    private static $trace;
    private static $headers;
    private static $headerCount;
    private static $content;
    private static $contentLength;
    private static $isError;
    private static $rootPath;
    private static $rootPathLength;
    private static $shouldHideExternal;
    private static $shouldHideTrace;
    private static $firstInternalStackFrameIndex;

    public static function execute(
        $source, array $headers = null, $content = null
    ) {
        self::$source = $source;
        self::$headers = $headers;
        self::$content = $content;
        self::$headerCount = count($headers);
        self::$contentLength = strlen($content);
        self::$isError = $source instanceof ErrorException;
        $rootPath = Config::getAppRootPath();
        $realRootPath = realpath($rootPath);
        if ($realRootPath !== false) {
            $rootPath = $realRootPath;
        } else {
            throw new ConfigException(
                "App root path '$rootPath' does not exist.'"
            );
        }
        self::$rootPath = $rootPath . DIRECTORY_SEPARATOR;
        self::$rootPathLength = strlen(self::$rootPath);
        self::$shouldHideTrace = false;
        self::$shouldHideExternal = false;
        self::$trace = null;
        if (self::$isError === false || self::$source->isFatal() === false) {
            if (self::$isError) {
                self::$trace = $source->getSourceTrace();
            } else {
                self::$trace = $source->getTrace();
            }
            if (self::isExternalFile($source->getFile())) {
                self::$firstInternalStackFrameIndex = null;
                foreach (self::$trace as $index => $frame) {
                    if (isset($frame['file'])
                        && self::isExternalFile($frame['file']) === false
                    ) {
                        self::$firstInternalStackFrameIndex = $index;
                        break;
                    }
                }
                if (self::$firstInternalStackFrameIndex !== null) {
                    self::$shouldHideExternal = true;
                    $maxIndex = count(self::$trace) - 1;
                    if ($maxIndex === self::$firstInternalStackFrameIndex) {
                        self::$shouldHideTrace = true;
                    }
                }
            }
        }
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
        if ($source instanceof ArgumentErrorException) {
            $message .= ', defined in '
                . $source->getFunctionDefinitionFile() . ' on line '
                . $source->getFunctionDefinitionLine();
        }
        $title = $type;
        if ($message !== '') {
            $message = htmlspecialchars(
                $message, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
            );
            $title .= ' - ' . $message;
        }
        echo '<!DOCTYPE html><html><head>',
            '<meta http-equiv="Content-Type"',
            ' content="text/html;charset=utf-8"/><title>', $title, '</title>';
        self::renderCss();
        echo '</head><body class="no-touch"><table id="page-container"><tbody>';
        self::renderHeader($type, $message);
        self::renderContent();
        self::renderJavascript();
        echo '</tbody></table></body></html>';
    }

    private static function isExternalFile($path) {
        $relativePath = self::getRelativePath($path);
        if ($relativePath === $path) {
            return true;
        }
        if (strncmp($relativePath, 'vendor' . DIRECTORY_SEPARATOR, 7) === 0) {
            return true;
        }
        return false;
    }

    private static function renderContent() {
        $hasTrace =
            self::$isError === false || self::$source->isFatal() === false;
        echo '<tr><td id="content"><table id="code"><tbody>',
            '<tr><td id="status-bar-wrapper">';
        self::renderStatusBar();
        echo '</td></tr><tr><td id="file-wrapper">';
        self::renderFile();
        echo '</td></tr>';
        if ($hasTrace) {
            echo '<tr><td id="stack-trace-wrapper"';
            if (self::$shouldHideTrace) {
                echo ' class="hidden"';
            }
            echo '>';
            self::renderStackTrace();
            echo '</td></tr>';
        }
        echo '</tbody></table></td></tr>';
    }

    private static function renderFile() {
        echo '<div id="file">';
        if (self::$shouldHideExternal) {
            $frame = self::$trace[self::$firstInternalStackFrameIndex];
            $path = $frame['file'];
            $errorLineNumber = $frame['line'];
            echo '<div id="internal-file"><h2>Internal File</h2>';
            self::renderFileContent($path, $errorLineNumber);
            echo '</div><div id="external-file" class="hidden">',
                '<h2>External File</h2>';
        }
        $path = self::$source->getFile();
        $errorLineNumber = self::$source->getLine();
        self::renderFileContent($path, $errorLineNumber);
        if (self::$shouldHideExternal) {
            echo '</div>';
        }
        echo '</div>';
    }

    private static function renderFileContent($path, $errorLineNumber) {
        self::renderPath(
            $path,
            true,
            ' <span class="line">Line ' . $errorLineNumber . '</span>'
        );
        echo '<table><tbody><tr><td class="index"><div class="index-content">';
        $lines = self::getLines($path, $errorLineNumber);
        foreach ($lines as $number => $line) {
            if ($number === $errorLineNumber) {
                echo '<div class="error-line-number"><div>',
                    $number, '</div></div>';
            } else {
                echo '<div class="line-number"><div>', $number, '</div></div>';
            }
        }
        echo '</div></td><td><pre class="content"><div>';
        foreach ($lines as $number => $line) {
            if ($number === $errorLineNumber) {
                echo '<span class="error-line"';
                echo '>', $line , "\n</span>";
            } else {
                echo $line , "\n";
            }
        }
        echo "</div></pre></td></tr></tbody></table>";
    }

    private static function renderStackTrace() {
        echo '<table id="stack-trace">',
            '<tr><td class="content"><h2>Stack Trace</h2><table><tbody>';
        $index = 0;
        $last = count(self::$trace) - 1;
        foreach (self::$trace as $frame) {
            if ($frame !== '{main}') {
                $invocation = StackTraceFormatter::formatInvocation($frame);
                echo '<tr id="frame-', $index, '"';
                if (self::$shouldHideExternal
                    && self::$shouldHideTrace === false
                ) {
                    if ($index < self::$firstInternalStackFrameIndex) {
                        echo ' class="hidden"';
                    }
                    echo '><td class="index">',
                        $index - self::$firstInternalStackFrameIndex;
                } else {
                    echo '><td class="index">', $index;
                }
                echo '</td><td class="value';
                if ($index === $last) {
                    echo ' last';
                }
                echo '"><code class="invocation">', $invocation, '</code>',
                    '<div class="position">';
                if (isset($frame['file'])) {
                    self::renderPath(
                        $frame['file'],
                        true,
                        ' <span class="line">Line ' . $frame['line'] . '</span>'
                    );
                } else {
                    echo '<span class="internal">internal function</span>';
                }
                echo '</div>';
                echo  '</td></tr>';
            }
            ++$index;
        }
        echo '</tbody></table></td></tr></table>';
    }

    private static function renderStatusBar() {
        echo '<table id="status-bar"><tbody><tr>';
        if (self::$shouldHideExternal) {
            echo '<td id="toggle-external-code">',
                '<a>Start from External File</a></td>';
        }
        echo '<td>';
        if (self::$shouldHideExternal) {
            echo '<div class="text">';
        }
        echo '<div class="first"><div>Response Headers:',
            ' <span class="first-value">',
            self::$headerCount, '</span></div><div>',
            'Content Size: <span>';
        if (self::$contentLength === 0) {
            echo '0 byte';
        } elseif (self::$contentLength === 1) {
            echo '1 byte';
        } else {
            $size = self::$contentLength / 1024;
            $prefix = '';
            $suffix = '';
            if ($size > 1) {
                $prefix = ' (';
                $suffix = ')';
                $tmp = $size / 1024; 
                if ($tmp > 1) {
                    $size = $tmp;
                    $tmp /= 1024;
                    if ($tmp > 1) {
                        echo sprintf("%.1f", $tmp), ' GB';
                    } else {
                        echo sprintf("%.1f", $size), ' MB';
                    }
                } else {
                    echo sprintf("%.1f", $size), ' KB';
                }
            }
            echo  $prefix, self::$contentLength, ' bytes', $suffix;
        }
        echo '</span></div></div><div class="second"><div>App Root Path:</div>',
            self::renderPath(self::$rootPath, false),
            '</div>';
        if (self::$shouldHideExternal) {
            echo '</div>';
        }
        echo '</td></tr></tbody></table>';
    }

    private static function getLines($path, $errorLineNumber) {
        $file = file_get_contents($path);
        $tokens = token_get_all($file);
        $firstLineNumber = 1;
        if ($errorLineNumber > 6) {
            $firstLineNumber = $errorLineNumber - 5;
        }
        $previousLineIndex = null;
        if ($firstLineNumber > 0) {
            foreach ($tokens as $index => $value) {
                if (is_string($value) === false) {
                    if ($value[2] < $firstLineNumber) {
                        $previousLineIndex = $index;
                    } else {
                        break;
                    }
                }
            }
        }
        $lineNumber = 0;
        $result = [];
        $buffer = '';
        foreach ($tokens as $index => $value) {
            if ($previousLineIndex !== null && $index < $previousLineIndex) {
                continue;
            }
            if (is_string($value)) {
                if ($value === '"') {
                    $buffer .= '<span class="string">' . $value . '</span>';
                } else {
                    $buffer .= '<span class="keyword">' . htmlspecialchars(
                        $value, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
                    ) . '</span>';
                }
                continue;
            }
            $lineNumber = $value[2];
            $type = $value[0];
            $content = str_replace(["\r\n", "\r"], "\n", $value[1]);
            $lines = explode("\n", $content);
            $lastLine = array_pop($lines);
            foreach ($lines as $line) {
                if ($lineNumber >= $firstLineNumber) {
                    $result[$lineNumber] =
                        $buffer . self::formatToken($type, $line);
                    $buffer = '';
                }
                ++$lineNumber;
            }
            $buffer .= self::formatToken($type, $lastLine);
            if ($lineNumber > $errorLineNumber + 5) {
                $buffer = false;
                break;
            }
        }
        if ($buffer !== false) {
            $result[$lineNumber] = $buffer;
        }
        if (isset($result[$errorLineNumber + 6])) {
            return array_slice(
                $result, 0, $errorLineNumber - $firstLineNumber + 6, true
            );
        }
        return $result;
    }

    private static function formatToken($type, $content) {
        $class = null;
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
        }
        $content = htmlspecialchars(
            $content, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
        );
        if ($class === null) {
            return $content; 
        }
        return '<span class="' . $class . '">' . $content . '</span>';
    }

    private static function renderPath(
        $path, $shouldRemoveRootPath = true, $suffix = ''
    ) {
        if ($shouldRemoveRootPath === true) {
            $path = self::getRelativePath($path);
        }
        echo '<div class="path"><code>', $path, '</code>', $suffix, '</div>';
    }

    private static function getRelativePath($path) {
        if (strncmp(self::$rootPath, $path, self::$rootPathLength) === 0) {
            $path = substr($path, self::$rootPathLength);
        }
        return $path;
    }

    private static function renderHeader($type, $message) {
        echo '<tr><td id="header"><h1>', $type, '</h1>';
        $message = trim($message);
        if ($message !== '') {
            echo '<div id="message">', $message, '</div>';
        }
        echo '<div id="nav"><div class="wrapper">',
            '<div class="selected" id="nav-code"><div>Code</div></div>',
            '<div id="nav-output"><a>Output</a></div></div></div></td></tr>';
    }

    private static function getMaxOutputContentSize($isText = false) {
        $size = strtolower(trim(Config::get(
            'hyperframework.web.debugger.max_output_content_size'
        )));
        if ($size === 'unlimited') {
            return -1;
        }
        if ($size === '') {
            if ($isText) {
                return '10MB';
            }
            return 10 * 1024 * 1024;
        }
        if ((int)$size <= 0) {
            return 0;
        }
        if (strlen($size) < 2) {
            return (int)$size;
        }
        $type = substr($size, -1);
        $size = (int)$size;
        switch ($type) {
            case 'g':
                if ($isText) {
                    return $size . 'GB';
                }
                $size *= 1024;
            case 'm':
                if ($isText) {
                    return $size . 'MB';
                }
                $size *= 1024;
            case 'k':
                if ($isText) {
                    return $size . 'KB';
                }
                $size *= 1024;
        }
        return $size;
    }

    private static function renderJavascript() {
        $isOverflow = false;
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
        $maxSize = self::getMaxOutputContentSize();
        if ($maxSize >=0 && self::$contentLength >= $maxSize) {
            $isOverflow = true;
            $content = mb_strcut(self::$content, 0, $maxSize);
        } else {
            $content = self::$content;
        }
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $content = json_encode(htmlspecialchars(
            $content, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
        ));
        $shouldHideTrace = 'null';
        $firstInternalStackFrameIndex = 'null';
        if (self::$shouldHideExternal) {
            if (self::$shouldHideTrace === true) {
                $shouldHideTrace = 'true';
                $firstInternalStackFrameIndex = 'null';
            } else {
                $shouldHideTrace = 'false';
                $firstInternalStackFrameIndex =
                    self::$firstInternalStackFrameIndex;
            }
        }
        if (self::$trace !== null) {
            $stackFrameCount = count(self::$trace);
        } else {
            $stackFrameCount = 0;
        }
?>
<script type="text/javascript">
document.body.ontouchstart = function() {
    document.body.className = '';
    isHandheld = true;
};
var isHandheld = false;
var codeContent = null;
var outputContent = null;
var fullContent = null;
var shouldHideTrace = <?= $shouldHideTrace ?>;
var stackFrameCount = <?= $stackFrameCount ?>;
var firstInternalStackFrameIndex = <?= $firstInternalStackFrameIndex ?>;
var content = <?= $content ?>;
function showOutput() {
    if (codeContent != null) {
        return;
    }
    var codeTab = document.getElementById("nav-code");
    codeTab.innerHTML = '<a href="javascript:showCode()">Code</a>';
    codeTab.className = '';
    var outputTab = document.getElementById("nav-output");
    outputTab.innerHTML = '<div>Output</div>';
    outputTab.className = 'selected';
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
    if (headers.length > 0) {
        outputContent = '<table id="output"><tbody><tr>'
            + '<td id="response-headers"><a'
            + ' href="javascript:toggleResponseHeaders()">'
            + '<span id="arrow" class="arrow-right"></span>'
            + '&nbsp;Headers <span id="header-count" class="header-count">'
            + headers.length + '</span></a>'
            + '<pre id="response-headers-content" class="hidden"><div>';
        var count = headers.length;
        for (var index = 0; index < count; ++index) {
            var header = headers[index];
            outputContent += '<code';
            if (count === index + 1) {
                outputContent += ' class="last"';
            }
            outputContent += '><span class="key">' + header[0]

                + ':</span> ' + header[1] + "\n</code>";
        }
        outputContent += '</div></pre></td></tr>';
    }
    if (isOverflow) {
        outputContent += '<tr><td class="notice"><span>Notice: </span>';
        var maxSize = <?= self::getMaxOutputContentSize() ?>;
        if (maxSize !== 0) {
            outputContent += 'Content is partial. Length is larger than'
                + ' output limitation ('
                + '<?= self::getMaxOutputContentSize(true) ?>' + ').';
        } else {
            outputContent += 'Content display is turn off.';
        }
        outputContent += '</td></tr></div>';
    }
    var responseBodyHtml = '';
    if (content != '') {
        responseBodyHtml += '<div id="toolbar"><a href="'
            + 'javascript:showRawContent()">Show Raw Content</a></div>';
    }
    responseBodyHtml += buildOutputContent(content);
    codeContent = contentDiv.innerHTML;
    contentDiv.innerHTML = outputContent
        + '<tr><td id="response-body" class="response-body">'
        + responseBodyHtml + '</td></tr>';
}

function showLineNumbers() {
    document.getElementById("response-body").innerHTML = '<div id="toolbar">'
        + '<a href="javascript:showRawContent()">Show Raw Content</a> </div>'
        + buildOutputContent(content);
}

function showRawContent() {
    var html = '<div id="toolbar">'
        + '<a href="javascript:showLineNumbers()">Show Line Numbers</a>'
    if (isHandheld == false) {
        html  += ' &nbsp;<a href="javascript:selectAll()">Select All</a>'
    }
    html += "</div><div id=\"raw\"><pre><div>" + content + "</div></pre></div>";
    document.getElementById("response-body").innerHTML = html;
}

function selectAll() {
    var text = document.getElementById('raw');
    if (window.getSelection) {
        var selection = window.getSelection();
        var range = document.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    } else if (document.body.createTextRange) {
        var range = document.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    }
}

function buildOutputContent(content) {
    var result = '';
    var lines = content.split("\n");
    var count = lines.length;
    var last = count - 1;
    var isCssLineNumber = false;
    var contentTag = 'pre';
    //for copy content
    if (typeof CSS != 'undefined' && typeof CSS.supports != 'undefined') {
        if (CSS.supports('white-space', 'pre-wrap')) {
            //for firefox
            contentTag = 'code';
        }
    }
    if (typeof window.getComputedStyle != 'undefined') {
        if (typeof window.getComputedStyle(document.body, null).content
            != 'undefined'
        ) {
            isCssLineNumber = true;
        }
    }
    for (var index = 0; index < count; ++index) {
        result += '<tr><td class="';
        if (count == 1) {
            result += 'first last ';
        } else if (index == 0) {
            result += 'first ';
        } else if (index == last) {
            result += 'last ';
        }
        result += 'line-number"';
        if (isCssLineNumber) {
            result += ' data-line="' + (index + 1) + '"';
        }
        result += '>';
        if (isCssLineNumber == false) {
            result += (index + 1);
        }
        result += '</td><td';
        if (count == 1) {
            result += ' class="first last"';
        } else if (index == 0) {
            result += ' class="first"';
        } else if (index == last) {
            result += ' class="last"';
        }
        result += '><' + contentTag + '>' + lines[index] + '</'
            + contentTag + '></td></tr>';
    }
    return '<table><tbody>' + result + '</tbody></table>';
}

function showCode() {
    if (codeContent == null) {
        return;
    }
    var codeTab = document.getElementById("nav-code");
    codeTab.innerHTML = '<div>Code</div>';
    codeTab.className = 'selected';
    var outputTab = document.getElementById("nav-output");
    outputTab.innerHTML = '<a href="javascript:showOutput()">Output</a>';
    outputTab.className = '';
    var contentDiv = document.getElementById("content");
    outputContent = contentDiv.innerHTML;
    contentDiv.innerHTML = codeContent;
    codeContent = null;
}

function toggleResponseHeaders() {
    var div = document.getElementById("response-headers-content");
    if (div.className == "hidden") {
        document.getElementById("arrow").className = 'arrow-bottom';
        div.className = "";
    } else {
        document.getElementById("arrow").className = 'arrow-right';
        div.className = "hidden";
    }
}

function showExternalFile() {
    document.getElementById("internal-file").className = "hidden";
    document.getElementById("external-file").className = "";
    var button = document.getElementById("toggle-external-code");
    if (shouldHideTrace) {
        document.getElementById('stack-trace-wrapper').className = '';
    } else {
        for (var index = 0; index < stackFrameCount; ++index) {
            var node = document.getElementById('frame-' + index);
            node.className = '';
            var child = node.firstChild;
            child.innerHTML =
                parseInt(child.innerHTML) + firstInternalStackFrameIndex;
        }
    }
    button.innerHTML =
        '<a href="javascript:showInternalFile()">Start from Internal File</a>';
}

function showInternalFile() {
    document.getElementById("internal-file").className = "";
    document.getElementById("external-file").className = "hidden";
    var button = document.getElementById("toggle-external-code");
    if (shouldHideTrace) {
        document.getElementById('stack-trace-wrapper').className = 'hidden';
    } else {
        for (var index = 0; index < stackFrameCount; ++index) {
            var node = document.getElementById('frame-' + index);
            if (index < firstInternalStackFrameIndex) {
                node.className = 'hidden';
            }
            var child = node.firstChild;
            child.innerHTML =
                parseInt(child.innerHTML) - firstInternalStackFrameIndex;
        }
    }
    button.innerHTML =
        '<a href="javascript:showExternalFile()">Start from External File</a>';
}

document.getElementById("nav-output").innerHTML =
    '<a href="javascript:showOutput()">Output</a>';
if (document.getElementById("toggle-external-code") !== null) {
    document.getElementById("toggle-external-code").firstChild.href =
        'javascript:showExternalFile()';
}
</script>
<?php
    }

    private static function renderCss() {
?>
<style>
body {
    background: #eee;
    font-family: Helvetica, Arial, sans-serif;
    font-size: 13px;
    color: #333;
    /* Prevent font scaling in landscape while allowing user zoom */
    -moz-text-size-adjust: 100%;
    -ms-text-size-adjust: 100%;
    -webkit-text-size-adjust: 100%;
}
table {
    border-collapse: collapse;
}
td {
    padding: 0;
}
a {
    text-decoration: none;
    color: #333;
}
.no-touch a:hover {
    color: #09d;
}
pre, h1, h2, body {
    margin: 0;
}
h2 {
    font-size: 18px;
    font-family: "Times New Roman", Times, serif;
    padding: 0 10px;
}
#message, code, pre {
    font-family: Consolas, "Liberation Mono", Menlo, Courier, monospace;
}
#page-container {
    width: 100%;
    min-width: 200px;
    _width: expression(
        (document.documentElement.clientWidth || document.body.clientWidth)
            < 200 ? "200px" : ""
    );
}
#header {
    background: #fff;
}
h1 {
    font-size: 21px;
    line-height: 25px;
    color: #e44;
    padding: 15px 10px 5px 10px;
}
h1, #message {
    font-weight: normal;
}
#message {
    font-size: 14px;
    padding: 0 10px 5px 10px;
    line-height: 20px;
}
#code, #output {
    border: 1px solid #ccc;
    width: 100%;
    background: #fff;
}
#nav {
    position: relative;
    height: 39px;
    border-bottom: 1px solid #ccc;
}
#nav a {
    color: #333;
    line-height: 28px;
    padding: 7px 25px 6px;
}
#nav a:hover {
    background: #eee;
}
#nav .wrapper {
    padding: 10px 0 0 10px;
    font-weight: bold;
    position: absolute;
}
#nav .wrapper div {
    overflow: hidden;
    float: left;
    line-height: 16px;
    border: 1px solid #fff;
    background-image: linear-gradient(#fcfcfc, #eee);
    background-color: #eee;
    border: 1px solid #ccc;
    border-bottom: 0;
    border-radius: 2px 2px 0 0;
}
#nav div.selected {
    border: 0;
    background: #eee;
    padding: 0;
    height: 32px;
}
#nav .selected div {
    border-bottom: 0;
    padding: 6px 25px 7px;
}
#nav-output {
    margin-left: 5px;
}
#content {
    padding: 10px;
}
#status-bar-wrapper {/* ie6 */
    color: #999;
    padding: 10px 0;
    border-bottom: 1px solid #ccc;
    background: #f8f8f8;
}
#status-bar {
    width: 100%;
    margin-right: 10px;
<?php if (self::$shouldHideExternal): ?>
    line-height: 25px;
<?php endif ?>
    font-size:12px;
}
#status-bar div.text {
    border-left: 1px dotted #ccc;
    _border-left: 1px solid #ddd;
}
#status-bar-wrapper div {
    float: left;
}
#status-bar-wrapper td{
    vertical-align: top;
}
#status-bar .7irst-value {
    margin-right: 10px;
}
#status-bar .second, #status-bar .first div {
    padding-left: 10px;
}
#status-bar span, #status-bar .path {
    color: #333;
}
#status-bar .path {
    padding-left: 3px;
}
.path {
    word-break: break-all; /* ie */
    word-wrap: break-word;
}
.header-count {
    border-radius: 8px;
    background: #eee;
    padding: 1px 6px;
}
#file-wrapper {
    padding: 10px 0;
    border-bottom: 1px solid #ccc;
}
#file .path {
    border-bottom: 1px dotted #ccc;
    _border-bottom: 1px solid #e1e1e1;
    font-size: 13px;
    padding: 5px 5px 8px 0;
    margin: 0 10px 10px 10px;
}
#response-body a, #toggle-external-code a {
    background-image: linear-gradient(#fcfcfc, #eee);
    background-color: #eee;
    border: 1px solid #d5d5d5;
    border-radius: 3px;
    padding: 4px 10px;
    font-size: 12px;
    word-break: keep-all;
    white-space: nowrap;
}
.no-touch #response-body a:hover, .no-touch #toggle-external-code a:hover {
    background-image: none;
    color: #333;
}
#toggle-external-code {
    width: 1px;
    padding-left: 10px;
}
#toggle-external-code a {
    margin-right: 10px;
    _display: inline-block;
    _line-height: 16px;
}
.hidden {
    display: none;
}
#file table {
    width: 100%;
    line-height: 18px;
}
#file pre {
    font-size: 13px;
    margin-right:10px;
    color: #00b;
}
#file .index .index-content {
    padding: 0;
    margin-left:10px;
}
#file .index {
    width:1px;
    text-align:right;
}
#file .index div {
    color:#aaa;padding:0 5px;
    font-size:12px;
}
#file .index .line-number {
    padding: 0 5px 0 0;
}
#file .line-number div {
    border-right:1px solid #e1e1e1;
}
#file .index .error-line-number {
    padding: 0 5px 0 0;
    background: #ffb;
}
#file .index .error-line-number div {
    background-color:#d11;
    color:#fff;
    text-shadow:1px 1px 0 rgba(0, 0, 0, .4);
    border-right:1px solid #e1e1e1;
}
#file pre .keyword {
    color: #070;
}
#file pre .string {
    color: #d00;
}
#file pre .comment {
    color: #f80;
}
#file pre .html {
    color: #000;
}
#file .error-line {
    display: block;
    background: #ffb;
}
#stack-trace {
    width: 100%;
}
#stack-trace .content {
    padding: 10px;
}
#stack-trace h2 {
    padding: 0 0 10px 0;
}
#stack-trace table {
    width: 100%;
    border-radius: 2px;
    border-spacing: 0; /* ie6 */
}
#stack-trace .path {
    color: #070;
}
#stack-trace .internal {
    color: #777;
}
#file .path .line, #stack-trace .line {
    font-size: 12px;
    color: #777;
    border-left: 1px solid #d5d5d5;
    padding-left: 8px;
}
#file .path code, #stack-trace .path code {
    padding-right: 3px;
}
#stack-trace table .value {
    padding: 10px 0 10px 5px;
    border-bottom: 1px dotted #ccc;
    _border-bottom: 1px solid #e1e1e1;
}
#stack-trace table .last {
    border-bottom: 0;
}
#stack-trace .index {
    padding: 10px 5px 0 5px;
    width: 1px;
    color: #aaa;
    font-size:12px;
    border-right: 1px solid #e1e1e1;
    text-align: right;
    vertical-align: top;
}
#stack-trace .position {
    color: #888;
    padding-top: 5px;
}
#stack-trace .invocation e1e1e1{
    word-break: keep-all;
    white-space: nowrap;
    padding-bottom: 7px;
    display: block;
}
#response-headers {
    padding: 10px;
    border-bottom: 1px solid #ccc;
}
#arrow {
    display: inline-block;
    width: 0;
    height: 0;
    width: 0;
    height: 0;
    line-height: 0;
    _filter: chroma(color=white);
    _font-size: 0;
    -moz-transform: scale(1.001);
}
.arrow-right {
    border-top: 4px solid transparent;
    border-bottom: 4px solid transparent;
    border-left: 4px solid #000;
    _border-top-color: white;
    _border-bottom-color: white;
}
.arrow-bottom {
    margin-bottom: 2px;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-top: 4px solid #000;
    _border-right-color: white;
    _border-left-color: white;
}
#output pre, #response-body code {
    white-space: pre-wrap;
    white-space: -moz-pre-wrap;
    white-space: -o-pre-wrap;
    word-wrap: break-word;
    word-break: break-all;
    _white-space: pre;
}
#response-headers-content {
    border: 1px solid #ddd;
    border-radius: 2px;
    border-collapse: separate;
    background: #f8f8f8;
    margin-top: 10px;
    padding: 5px;
}
#response-headers-content code {
    word-break: break-all; /* ie */
　　word-wrap: break-word;
    padding: 5px;
    display: block;
    border-bottom: 1px dotted #ddd;
    _border-bottom: 1px solid #e1e1e1; /* ie6 */
}
#response-headers-content .key {
    word-break: keep-all;
    white-space: nowrap;
    font-weight: bold;
}
#response-headers-content .last {
    border-bottom: 0;
}
#response-body {
    padding: 10px;
    background:#f8f8f8;
}
#response-body table {
    line-height: 18px;
}

#output-button-top-wrapper {
    margin-bottom: 10px;
}
#output-button-bottom-wrapper {
    margin-top: 10px;
}
#header-count {
    color: #333;/* ie6 */
}
#response-body table {
    background-color: #fff;
    line-height:18px;
    width: 100%;
    border:1px solid #e1e1e1;
    border-radius: 2px;
}
#response-body td {
    padding: 0 5px;
}
#response-body td.first {
    padding-top: 5px;
}
#response-body td.last {
    padding-bottom: 5px;
}
#response-body .line-number {
    background-color: #f8f8f8;
    border-right:1px solid #e1e1e1;
    font-size: 11px;
    color: #999;
    text-align:right;
    vertical-align: top;
    padding: 0 5px;
    width: 1px;
}
#response-body .line-number:before {
    content: attr(data-line);
}
.notice {
    background: #ff9;
    padding: 10px;
}
.notice span {
    font-weight: bold;
}
#raw {
    width: 100%; 
    border: 1px solid #e1e1e1;
    background: #fff;
}
#raw pre {
    padding: 5px;
}
#toolbar {
    padding-bottom: 10px;
    line-height: 24px;
}
</style>
<?php
    }
}
