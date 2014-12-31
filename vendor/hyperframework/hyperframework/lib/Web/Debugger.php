<?php
namespace Hyperframework\Web;

use ErrorException;
use Hyperframework\Common\StackTraceFormatter;
use Hyperframework\Common\FileLoader;

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
        $content = "a            a\n \nb\n \n";
        for ($i = 0; $i < 1000; ++$i) {
            for ($j = 0; $j < 100; ++$j) {
                $content .= $j;
            }
            $content .= "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaisdfffffffffvwfffffffffffffffffffffffffffffff ffffffffffffffffffffffffffffffffffffffffffffffffffffffffifif fffffffffffffffffffffffffffffff ffffffffffffffffffffffffffffffffffffffffffffffffffffffffifif eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeffffffffffffffffffffffffffffff ffffffffffffffffffffffffffffffffffffffffffffffffffffffffifif ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffe" . "\n";
            $content .= "x\n";
        }
        self::$content = $content;//file_get_contents('/home/az/logo.jpg');//$content;
        self::$headerCount = count($headers);
        self::$contentLength = strlen($content);
        self::$isError = $source instanceof ErrorException;
        self::$rootPath = FileLoader::getDefaultRootPath()
            . DIRECTORY_SEPARATOR;
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
            if (self::isExternalPath($source->getFile())) {
                self::$firstInternalStackFrameIndex = null;
                foreach (self::$trace as $index => $frame) {
                    if (isset($frame['file'])
                        && self::isExternalPath($frame['file']) === false
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
        $title = $type;
        if ($message !== '') {
            $message = htmlspecialchars(
                $message, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
            );
            $title .= ' - ' . $message;
        }
        echo '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8"/><title>', $title, '</title>';
        self::renderCss();
        echo '</head><body><table id="page-container"><tbody>';
        self::renderHeader($type, $message);
        self::renderContent();
        self::renderJavascript();
        echo '</tbody></table></body></html>';
    }

    private static function isExternalPath($path) {
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
        echo '<tr><td id="content"><table id="code"><tbody><tr><td id="status-bar-wrapper">';
        self::renderStatusBar();
        echo '</td></tr><tr><td id="file-wrapper"';
        if (self::$shouldHideTrace || $hasTrace === false) {
            echo ' class="last"';
        }
        echo '">';
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
        echo '<div id="file"><h2><div>File</div></h2>';
        if (self::$shouldHideExternal) {
            $frame = self::$trace[self::$firstInternalStackFrameIndex];
            $path = $frame['file'];
            $errorLineNumber = $frame['line'];
            echo '<div id="internal-file">';
            self::renderFileContent($path, $errorLineNumber);
            echo '</div>';
            echo '<div id="external-file" class="hidden">';
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
        self::renderPath($path);
        echo '<table><tbody><tr><td class="index" style="border:0"><div class="index-content">';
        $lines = self::getLines($path, $errorLineNumber);
        foreach ($lines as $number => $line) {
            if ($number === $errorLineNumber) {
                echo '<div style="padding: 0 5px 0 0;background:#ff9"><div style="border-right:1px solid #e1e1e1" class="error-line-number">', $number, '</div></div>';;
            } else {
                echo '<div style="padding: 0 5px 0 0"><div style="border-right:1px solid #e1e1e1">', $number, '</div></div>';;
            }
        }
        echo '</div></td><td><pre class="content" style="height:auto;padding-left:0;">';
        foreach ($lines as $number => $line) {
            echo '';
            if ($number === $errorLineNumber) {
                echo '<span class="error-line" style="display:block;"';
                echo '>', $line , "\n</span>";
            } else {
                echo '', $line , "\n";
            }
//            echo '>', $line . 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxe', '</pre>';
        }
        echo '</pre></td></tr></tbody></table>';
    }

    private static function renderStackTrace() {
        echo '<table id="stack-trace"><tr><td class="content"><h2>Stack Trace</h2><table><tbody>';
        $index = 0;
        $last = count(self::$trace) - 1;
        foreach (self::$trace as $frame) {
            if ($frame !== '{main}') {
                $invocation = StackTraceFormatter::formatInvocation($frame);
                echo '<tr id="frame-', $index, '"';
                if (self::$shouldHideExternal
                    && self::$shouldHideTrace === false
                ) {
                    if ($index <= self::$firstInternalStackFrameIndex) {
                        echo ' class="hidden"';
                    }
                    echo '><td class="index">', $index - self::$firstInternalStackFrameIndex - 1;
                } else {
                    echo '><td class="index">', $index;
                }
                echo '</td><td class="value';
                if ($index === $last) {
                    echo ' last';
                }
                echo '"><code class="invocation">', $invocation, '</code>';
//                echo '</td><td class="value"><div class="invocation">', $invocation , 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxe',
                    '</div>';
                echo '<div class="position">';
                if (isset($frame['file'])) {
                    self::renderPath($frame['file'], true, ' <span class="line">' . $frame['line'] . '</span>');

//                    self::renderPath($frame['file'] . 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', true, ' <span class="line">' . $frame['line'] . '</span>');
                } else {
                    echo  'internal function';
                }
                echo '</div>';
                echo  '</td></tr>';
            }
            ++$index;
        }
        echo '</tbody></table></td></tr></table>';
    }

    private static function renderStatusBar() {
        echo '<div id="status-bar">';
        if (self::$shouldHideExternal) {
            echo '<div id="toggle-external-code"><a>Show External File</a></div>';
        }
        echo '<div class="first"><div>Response Headers:',
            ' <span class="number first-value">',
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
        echo '</span></div></div><div class="second"><div>App Root Path:</div>';
//            self::renderPath(FileLoader::getDefaultRootPath() . 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxe', false);
            self::renderPath(FileLoader::getDefaultRootPath(), false);
        echo '</div></div>';
    }

    private static function getLines($path, $errorLineNumber) {
        $file = file_get_contents($path);
        $tokens = token_get_all($file);
        $firstLineNumber = 1;
        if ($errorLineNumber > 11) {
            $firstLineNumber = $errorLineNumber - 10;
        }
        $lineNumber = null;
        $result = [];
        $buffer = '';
        $previousValue = null;
        foreach ($tokens as $index => $value) {
            if (is_string($value)) {
                if ($lineNumber < $firstLineNumber) {
                    continue;
                }
                if ($value === '"') {
                    $buffer .= '<span class="string">' . $value . '</span>';
                } else {
                    $buffer .= '<span class="keyword">' . htmlspecialchars(
                        $value, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
                    ) . '</span>';
                }
                continue;
            }
            if ($value[2] < $firstLineNumber) {
                $previousValue = $value;
                continue;
            }
            $lineNumber = $value[2];
            $type = $value[0];
            $content = $value[1];
            if ($previousValue !== null) {
                if ($previousValue[0] === T_WHITESPACE) {
                    $tmp = str_replace(["\r\n", "\r"], "\n", $previousValue[1]);
                    $lines = explode("\n", $tmp);
                    $content = end($lines) . $content;
                }
                $previousValue = null;
            }
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
                //$class = token_name($type);
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
        echo '<div class="path">', str_replace(
            DIRECTORY_SEPARATOR,
            '<span class="separator">' . DIRECTORY_SEPARATOR . '</span>',
            $path
        ), $suffix, '</div>';
    }

    private static function getRelativePath($path) {
        if (strncmp(self::$rootPath, $path, self::$rootPathLength) === 0) {
            $path = substr($path, self::$rootPathLength);
        }
        return $path;
    }

    private static function renderHeader($type, $message) {
        echo '<tr><td id="header"><h1>', $type, '</h1><div id="message">',
            $message, '</div>',
            '<div id="nav"><div class="wrapper"><div class="selected" id="nav-code"><div>',
            'Code</div></div><div id="nav-output"><a>Output</a></div></div></div></td></tr>';
    }

    private static function renderJavascript() {
        $isOverflow = false;
//        $hiddenContent = null;
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
 //       $maxInitContentLength = 1;//256 * 1024;
 //       if (self::$contentLength > $maxInitContentLength) {
 //           $tmp = $content;
 //           $content = mb_strcut($tmp, 0, $maxInitContentLength);
 //           $hiddenContent = substr($tmp, strlen($content));
 //       }
        $content = json_encode(htmlspecialchars(
            $content, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
        ));
 //       if ($hiddenContent !== null) {
 //           $hiddenContent = json_encode(htmlspecialchars(
 //               $hiddenContent, ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
 //           ));
 //       } else {
 //           $hiddenContent = 'null';
 //       }
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
var isIeSix = false;
</script>
<!--[if IE 6]>
<script type="text/javascript">
isIeSix = true;
</script>
<![endif]-->
<script type="text/javascript">
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
    document.getElementById("nav-code").innerHTML = '<a href="javascript:showCode()">Code</a>';
    document.getElementById("nav-code").className = '';
    document.getElementById("nav-output").innerHTML = '<div>Output</div>';
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
    if (headers.length > 0) {
        outputContent = '<table id="output"><tbody><tr><td id="response-headers">'
            + '<a id="show-response-headers-botton"'
            + ' href="javascript:toggleResponseHeaders()">'
            + '<span id="arrow">►</span> Headers <span id="header-count" class="header-count">' + headers.length
            + '</span></a><pre id="response-headers-content" class="hidden">';
        var count = headers.length;
        for (var index = 0; index < count; ++index) {
            var header = headers[index];
            outputContent += '<code';
            if (count === index + 1) {
                outputContent += ' class="last"';
            }
            outputContent += '><span class="key">' + header[0]

                + ':</span> ' + header[1] + "\n</code>";
//                + ':</span> ' + header[1] + "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\n</code>";
        }
        outputContent += '</pre></td></tr>';
    }
    if (isOverflow) {
        outputContent += '<tr><td class="notice"><span>Notice: </span>Content is partial.'
            + ' Length is larger than output limitation (10MB).</td></tr></div>';
    }
    var responseBodyHtml = '<div id="toolbar"><a href="javascript:showRawContent()">Show Raw Content</a> </div>' + buildOutputContent(content);
    codeContent = contentDiv.innerHTML;
    contentDiv.innerHTML = outputContent + '<tr><td id="response-body" class="response-body">'
        + responseBodyHtml + '</td></tr>';
}

function showLineNumbers() {
    document.getElementById("response-body").innerHTML =
'<div id="toolbar"><a href="javascript:showRawContent()">Show Raw Content</a> </div>' + buildOutputContent(content);
}

function showRawContent() {
    document.getElementById("response-body").innerHTML
        = '<div id="toolbar"><a href="javascript:showLineNumbers()">Show Line Numbers</a> &nbsp;<a href="javascript:selectAll()">Select All</a></div>' 
       + '<div id="raw"><pre>' + content + '</pre></div>';//buildOutputContent(fullContent);
   
}

function selectAll() {
    var doc = document;
    var text = doc.getElementById('raw');    
    if (window.getSelection) { // moz, opera, webkit
        var selection = window.getSelection();
        var range = doc.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    } else if (doc.body.createTextRange) { // ms
        var range = doc.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    }
}

//function showHiddenContent() {
//    document.getElementById("output-button-top-wrapper").style.display
//        = 'none';
//    document.getElementById("output-button-bottom-wrapper").style.display
//        = 'none';
//    document.getElementById("response-body").innerHTML
//        = buildOutputContent(fullContent);
//    fullContent = null;
//}

function buildOutputContentForIe(content) {
    var result = '';
    var lines = content.split("\n");
    var count = lines.length;
    var last = count - 1;
    for (var index = 0; index < count; ++index) {
        result += '<li';
        if (count == 1) {
            result += ' class="first last"';
        } else if (index == 0) {
            result += ' class="first"';
        } else if (index == last) {
            result += ' class="last"';
        }
        if (lines[index] == '' && isIeSix == false) {
            result += ' style="height:18px"'; //firefox, ie6 has bug
        }
        result += '><code>' + lines[index] + '</code></li>';
    }
    var prefix = 'padding-left';
    extra = 12;
    if (isIeSix) {
        extra = 20;
        prefix = 'margin-left';
    }
    return '<ol style="' + prefix + ':' + (count.toString().length * 7 + extra) + 'px">'
        + result + '</ol>';
}
String.prototype.endWith=function(str){
if(str==null||str==""||this.length==0||str.length>this.length)
  return false;
if(this.substring(this.length-str.length)==str)
  return true;
else
  return false;
return true;
}

function buildOutputContent(content) {
    var result = '';
    var lines = content.split("\n");
    var count = lines.length;
    var last = count - 1;
    for (var index = 0; index < count; ++index) {
        result += '<tr><td class="';
        if (count == 1) {
            result += 'first last ';
        } else if (index == 0) {
            result += 'first ';
        } else if (index == last) {
            result += 'last ';
        }
        result += 'line-number">' + (index + 1)
            + '</td><td';
        if (count == 1) {
            result += ' class="first last"';
        } else if (index == 0) {
            result += ' class="first"';
        } else if (index == last) {
            result += ' class="last"';
        }
        result += '><pre>' + lines[index] + '</pre></td></tr>';
    }
    return '<table><tbody>' + result + '</tbody></table>';
}

function showCode() {
    if (codeContent == null) {
        return;
    }
    document.getElementById("nav-code").innerHTML = '<div>Code</div>';
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
    if (div.className == "hidden") {
        document.getElementById("arrow").innerHTML = '▼';
        div.className = "";
    } else {
        document.getElementById("arrow").innerHTML = '►';
        div.className = "hidden";
    }
}

function showExternalFile() {
    document.getElementById("internal-file").className = "hidden";
    document.getElementById("external-file").className = "";
    var button = document.getElementById("toggle-external-code");
    if (shouldHideTrace) {
        document.getElementById('stack-trace-wrapper').className = '';
        document.getElementById('file-wrapper').className = '';
    } else {
        for (var index = 0; index < stackFrameCount; ++index) {
            var node = document.getElementById('frame-' + index);
            node.className = '';
            var child = node.firstChild;
            child.innerHTML = parseInt(child.innerHTML)
                + firstInternalStackFrameIndex + 1;
        }
    }
    button.innerHTML = '<a href="javascript:showInternalFile()">Show Internal File</a>';
}

function showInternalFile() {
    document.getElementById("internal-file").className = "";
    document.getElementById("external-file").className = "hidden";
    var button = document.getElementById("toggle-external-code");
    if (shouldHideTrace) {
        document.getElementById('stack-trace-wrapper').className = 'hidden';
        document.getElementById('file-wrapper').className = 'last';
    } else {
        for (var index = 0; index < stackFrameCount; ++index) {
            var node = document.getElementById('frame-' + index);
            if (index <= firstInternalStackFrameIndex) {
                node.className = 'hidden';
            }
            var child = node.firstChild;
            child.innerHTML = parseInt(child.innerHTML)
                - firstInternalStackFrameIndex - 1;
        }
    }
    button.innerHTML = '<a href="javascript:showExternalFile()">Show External File</a>';
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
a:hover {
    color: #09d;
}
pre, h1, h2, body {
    margin: 0;
}
h2 {
    font-size: 18px;
    font-family: "Times New Roman", Helvetica, Arial, sans-serif;
    padding: 0 10px;
}
#page-container {
    height: 100%;
    width: 100%;
/*
    min-width: 200px;
    _width: expression(
        (document.documentElement.clientWidth || document.body.clientWidth)
            < 200 ? "200px" : ""
    );
*/
}
#header {
    background-color: #c22;
}
h1, #message {
    color: #fff;
    padding: 10px;
    font-weight: normal;
    font-size: 22px;
    text-shadow: 1px 1px 0 rgba(0, 0, 0, .4);
}
#message {
    font-size:16px;
    padding-top: 0;
    line-height: 20px;
}
#code, #output {
    border: 1px solid #ccc;
    width: 100%;
    background: #fff;
}
#nav {
    position: relative;
    height: 37px;
    border-bottom: 1px solid #ccc;
    background: #f8f8f8;
}
#nav .wrapper {
    padding: 8px 0 0 10px;
    font-weight: bold;
    position: absolute;
}
#nav .wrapper div {
    float: left;
    line-height: 16px;
    padding: 6px 25px;
    border: 1px solid #f8f8f8;
    border-bottom: 0;
}
#nav div.selected {
    border: 0;
    background: #eee;
    padding: 0;
    height: 32px;
}
#nav .selected div {
    border: 1px solid #ccc;
    border-bottom: 0;
    padding: 6px 25px 7px;
    border-radius: 2px 2px 0 0;
}
#content {
    padding: 10px;
}
#status-bar-wrapper {/* ie6 */
    color: #999;
    padding: 10px 0;
    border-bottom: 1px solid #ccc;
}
#status-bar {
    padding-right: 10px;
<?php if (self::$shouldHideExternal): ?>
    line-height: 25px;
<?php endif ?>
    font-size:12px;
}
<?php if (self::$shouldHideExternal): ?>
#status-bar .first div, #status-bar .second {
    line-height: 18px;
    padding-top: 3px;
}
<?php endif ?>
#status-bar-wrapper div {
    float: left;
}
#status-bar .first-value {
    margin-right: 10px;
}
#status-bar .first {
    padding-left: 10px;
    word-break: keep-all;
    white-space: nowrap;
}
#status-bar span, #status-bar .path {
    color: #333;
}
#status-bar .path {
    padding-left: 3px;
}
#status-bar .second {
    padding-left: 10px;
}
#status-bar .separator {
    color: #999;
}
.path {
    word-break: break-all; /* ie */
　　word-wrap: break-word;
    font-family: Helvetica, Arial, sans-serif;
}
.separator {
    padding: 0 2px;
    color: #999;
}
#status-bar .number, .header-count {
    border-radius: 8px;
    background: #eee;
    padding: 1px 6px;
}
#file-wrapper {
    padding: 10px 0;
    border-bottom: 1px solid #ccc;
}
#file-wrapper.last {
    border-bottom: 0;
}
#file .path {
    font-size: 13px;
    font-weight: normal;
    padding: 5px 0 10px 10px;
}
#toggle-external-code a:hover {
    background-image: none;
    color: #333;
}
#toggle-external-code a {
    background-image: linear-gradient(#fcfcfc, #eee);
    background-color: #eee;
    border: 1px solid #d5d5d5;
    border-radius: 3px;
    text-shadow: 0 1px 0 rgba(255,255,255,0.9);
    padding: 4px 10px;
}
#toggle-external-code {
    margin-left: 10px;
    display: inline; /* ie6 */
}
.hidden {
    display: none;
}
#file table {
    width: 100%;
    line-height: 18px;
}
#file .content {
    margin-right:10px;
}
#file .index .index-content {
    padding: 0;
    margin-left:10px;
}
#file .index {
    width:1px;
    text-align:right;border-right:1px solid #e1e1e1;
}
#file .index div {
color:#aaa;padding:0 5px;
font-size:12px;
}
#file div.error-line-number {
background-color:#c22;color:#fff;text-shadow:1px 1px 0 rgba(0, 0, 0, .4)
}
#file pre {
    height: 18px;
    padding-left: 10px;
    color: #0000BB;
}
#file pre .keyword {
    color: #007700;
}
#file pre .string {
    color: #DD0000;
}
#file .error-line {
    background: #ff9;
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
  border-spacing: 0; /* inline for ie6 */
 }
#stack-trace .path {
    color: #070;
}
#stack-trace .line{
    color: #777;
    background: #f1f1f1;
    border: 1px solid #ddd;
    border-top: 1px solid #eee;
    border-left: 1px solid #eee;
    padding: 2px 3px;
    line-height: 18px;
    border-radius: 3px;
    font-size: 12px;
    word-break: keep-all;
    white-space: nowrap;
}
#stack-trace table .value {
    padding: 8px 0 8px 10px;
}
#stack-trace table .last {
    border: 0;
}
#stack-trace .index {
    padding: 9px 5px 0 5px;
    width: 1px;
    color: #aaa;
    font-size:12px;
    border-right: 1px solid #e1e1e1;
    text-align: right;
    vertical-align: top;
}
#stack-trace .position {
    line-height: 18px;
}
#stack-trace .invocation {
    word-break: keep-all;
    white-space: nowrap;
    padding-bottom: 5px;
    font-size:14px;
    display: block;
}
#response-headers {
    padding: 10px;
    border-bottom: 1px solid #ccc;
}
#output pre {
    white-space: pre-wrap;       /* CSS 3 */
    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
    white-space: -pre-wrap;      /* Opera 4-6 */
    white-space: -o-pre-wrap;    /* Opera 7 */
    word-wrap: break-word;       /* Internet Explorer 5.5+ */
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
#response-body a {
    background-image: linear-gradient(#fcfcfc, #eee);
    background-color: #eee;
    border: 1px solid #d5d5d5;
    border-radius: 3px;
    text-shadow: 0 1px 0 rgba(255,255,255,0.9);
    padding: 4px 10px;
    font-size: 12px;
    line-height: 24px;
}
#output-button-top-wrapper {
    margin-bottom: 10px;
}
#output-button-bottom-wrapper {
    margin-top: 10px;
}
#response-body a:hover {
    background-image: none;
    color: #000;
}
#arrow {
    font-size: 10px;
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
    padding-bottom:10px;
}
</style>
<?php
    }
}
