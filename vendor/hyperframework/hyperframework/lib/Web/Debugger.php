<?php
namespace Hyperframework\Web;

use Exception;
use ErrorException;
use Hyperframework\Common\StackTraceFormatter;
use Hyperframework\Common\FileLoader;

class Debugger {
    public static function execute(
        $exception, $headers = null, $outputBuffer = null
    ) {
        $isError = $exception instanceof ErrorException;
        $isHeadersSent = headers_sent();
        if (headers_sent() === false) {
            header('Content-Type: text/html; charset=UTF-8');
        }
        echo '<div style="background:#fff">';
        echo '<h2 style="line-height:25px;font-size:22px;color:black;padding:0;font-weight:normal;margin:0">';
            echo '<span style="color:white;margin-bottom:8px;font-family:Arial;width:100%;display:block;font-size:18px;red;background:#c22;padding:10px 10px;padding-left:10px;text-shadow:1px 1px 0 rgba(0, 0, 0, .4)">';
        if ($isError) {
            if ($exception->shouldThrow() === true) {
                echo 'Error Exception';
            } elseif ($exception->getSeverityAsString() === 'error') {
                echo 'Fatal Error';
            } else {
                echo ucwords($exception->getSeverityAsString());
            }
        } else {
            echo get_class($exception);
            //echo 'Exception';
        }
        if ((string)$exception->getMessage() !== '') {
            echo '<div style="margin:0px 0 0 0px;font-size:16px">', $exception->getMessage(), '</div>';
        }
        echo '</span> ';
        if ($isError === false){
           // echo ' <span style="font-family:arial;font-size:12px;color:#999;background:">#code: <span style="color:#999">', $exception->getCode() . '</span></span>';
        }
        echo '</h2>';
        echo '<div style="line-height:20px;color:#ccc;padding:5px 0 5px 15px;padding-left:10px;font-size:13px;border-bottom:1px solid  #ccc">';
        echo '<style>body{margin:0;padding:0;background:#eee;}.tab {background:#ddd;border-bottom:1px solid;border-color:#eee;font-family:Arial;color:#555;padding:5px 25px;margin:5px 1px;}.tab:hover{color:#333;background:#ccc}</style>';
        echo '<span class="tab" style="border-radius:2px 2px 0;font-weight:bold;background:#eee;border:1px;border:1px solid #ccc;border-bottom:0px;margin-top:-5px;float:left;float:left;color:#333;text-decoration:none;">Code</span>';
        $_COOKIES['xx'] = 'x';
        setcookie('xx', 'xxxx');
        setcookie('yy', 'xxxx');
        //ob_end_flush();
//      print_r(headers_list());
//      echo 'Response:';
        echo '<script>
            var contentHtml;
            function show() {
                var content = document.getElementById("content");
                contentHtml = content.innerHTML;
                ';
                //$preview = addslashes(var_export($headers, true));
                //$preview = str_replace("\n", '\n', $preview);
                $buffer = file_get_contents('/home/az/20101220224643434.jpg');
                $len = strlen($buffer);
                if ($len > 1024 * 10) {
                    $buffer = mb_strcut($buffer, 0, 1024 * 1024 * 10);
                    echo 'notice= "<div style=\"padding:5px;background:#ff6;color:333\">Notice: Content is partial. Content-length is larger than output limitation(10m) ', $len, '</div>";';
                }
                $len = strlen($buffer);
                $buffer2 = null;
                $max = 1024 * 4;
                if ($len > $max) {
                    $tmp = $buffer;
                    $buffer= mb_strcut($tmp, 0, $max);
                    $buffer2 = substr($tmp, strlen($buffer));
                }
                $o = '<div><a href="javascript:void(0)">► Headers</a></div>';
                foreach ($headers as $i) {
                    $tmp = explode(':', $i, 2);
                    $o.= '<div><span>' . $tmp[0] . ':<span> <span>'. $tmp[1]. '</span>';
                }
                $o .= '<hr/>';
                echo 'headers =',  json_encode($o), ';';
                $buffer = htmlspecialchars($buffer, ENT_SUBSTITUTE);
                $buffer = str_replace("\r\n", '>', $buffer);
                $buffer = str_replace("\n", '>', $buffer);
                $buffer = str_replace("\r", '>', $buffer);
                echo 'buffer = ', json_encode($buffer), ';';
                if ($buffer2 !== null) {
                    $buffer2 = htmlspecialchars($buffer2, ENT_SUBSTITUTE);
                    $buffer2 = str_replace("\r\n", '>', $buffer2);
                    $buffer2 = str_replace("\n", '>', $buffer2);
                    $buffer2 = str_replace("\r", '>', $buffer2);
                    echo 'buffer2 = ', json_encode($buffer2), ';';
                }
                echo '
    contentx = buildOutput(buffer);
    if (buffer2 != null) {
        contentx = "<div class=\"more\" onclick=\"showmore()\">Show hidden content</div>" + contentx
        +"<div onclick=\"showmore()\" class=\"more\">Show hidden content</div>";
    }
    content.innerHTML = headers + notice + contentx;
            }
var headers;
var buffer;
var buffer2;
var notice;
function buildOutput(rows) {
                 bufferx = rows.split(">");
                var b = "<table>";
                var count = 0;
                for (var index in bufferx) {
                    ++count;
                    var p = ++index;
                    --index;
                    b += "<tr><td class=\"line-number\" style=\'vertical-align:top;padding-right:5px;border-right:1px solid #ccc;text-align:right;font-size:12px;font-family:arial\' line-number=\"" + count + "\"></td><td id=\"srouce-" + index + "\" class=\"source\" style=\"padding-left:10px;\">" + bufferx[index] + "</td></tr>";
                }
                    return b + "</table>";
   
    }
    function showmore() {
        content.innerHTML = headers + notice + buildOutput(buffer + buffer2);
    }
            </script> ';
        echo '<a href="javascript:void(0)" onclick="show()" style="font-family:arial;font-weight:bold;margin-left:20px;font-size:13px;">Output</a>';
        echo '</div>';
        echo '</div>';
        $contentLength = strlen($outputBuffer);
        $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        $suffix = 's';
        if ($executionTime < 1) {
            $executionTime = $executionTime * 1000;
            $suffix = 'ms';
        }
        $executionTime = sprintf("%.3f", $executionTime) . $suffix;
        $suffix = 'K';
        $memory = memory_get_peak_usage(true) / 1024;
        if ($memory >= 1024) {
            $suffix = 'M';
            $memory = $memory / 1024;
        }
        $memory = (float)sprintf("%.3f", $memory) . $suffix;
        echo '<style>h2 {margin-top:0}#stat span {margin-right:10px;white-space:nowrap;}#stat b {color:#777}</style>';
    echo '<style>
        a {
            color:#333;
            text-decoration:none;
    }
    .source {
        word-break:break-all; /*支持IE，chrome，FF不支持*/
　　    word-wrap:break-word;/*支持IE，chrome，FF*/
    }
    .more {
        width:200px;
        text-align:center;
        background:#eee;
        padding:5px 10px;
        border:1px solid #ddd;
    }
    a:hover {
            text-decoration:underline;
    }
        #stat span.x{
          margin-right:0;
        }
    #stat {
    line-height:20px;font-family:arial;font-size:12px;margin:5px 0 2px 10px;color:#999;padding-bottom:5px;
    }
    @media all and (max-width: 600px) {
        #stat {
            text-align:center;
        }
    }
    h2 {
        font-size:20px;
        margin-left:2px;
        color:#333;
        margin-bottom:10px;
    }
    .line-number:before {
        content:attr(line-number);
        color:#999;
    }
table {
    border-collapse: collapse;
    border:0;
}
td {
    padding:0;
    }
    code {
        white-space:nowrap;
    }
    #stat b {
    color: #333;
border-radius:8px;
font-weight:normal;
background:#eee;
padding:1px 6px;
    }
    #trace .box {
color:#999;
border-right:2px solid #ddd;
    }
    #trace .box:hover,#trace .frame {
       cursor:pointer;
        color:#000;
    border-right:2px solid #999;
    }
    </style>';
    //echo ' <span class="x"><span>Execution Time: <b>',$executionTime,'</b></span> <span>Memory Peak: <b>', $memory, '</b></span></span></div>';
        $firstLinePrefix = null;
    $appRootPath = FileLoader::getDefaultRootPath();
    $rootPath = str_replace(DIRECTORY_SEPARATOR, '<i style="font-style:normal;color:#999;padding:0 3px">' . DIRECTORY_SEPARATOR . '</i>', $appRootPath);
        echo '<div id="content" style="margin-top:10px;margin-bottom:20px;margin-right:10px;margin-left:10px;background:#fff;padding:2px 10px 10px;border-radius:2px;border:1px solid #ccc">';
    echo '<div id="stat" style=""><span class="x"><span>Response Headers: <b>',count($headers),'</b></span> <span>Content Length: <b>',$contentLength,'</b></span></span> App Root Path: <span style="color:#333">', $rootPath, '</span></div>';
    $file = $exception->getFile();
    $filePrefix = $appRootPath . DIRECTORY_SEPARATOR;
    $filePrefixLength = strlen($filePrefix);
    if (strncmp($filePrefix, $file, $filePrefixLength) === 0) {
        $file = substr($file, $filePrefixLength);
    }
    $file = str_replace(DIRECTORY_SEPARATOR, '<span style="color:#999;padding:0 3px">' . DIRECTORY_SEPARATOR . '</span>', $file);
        echo '<h2 style="border-top:1px solid #ddd;padding:10px 0  0">File <span style="line-height:20px;font-family:arial;font-size:13px;font-weight:normal;color:#333;">',$file, '</span></h2>';
        $lines = self::toArray((token_get_all(file_get_contents($exception->getFile()))));
        $index = 1;
        $count = count($lines);
        $errorLine = $exception->getLine() - 1;
        echo '<table style="line-height:17px;width:100%"><tr><td width="1px;"><code style="float:left;text-align:right;border-right:1px solid #e1e1e1;">';
        foreach ($lines as $key => $line) {
            if ($index + 9 < $errorLine || $index - 11 > $errorLine) {
                ++$index;
                continue;
            }
            echo '<div style="font-size:12px;font-family:arial;padding-right:5px;';
            if ($index === $errorLine + 1) {
                echo 'background-color:ff6;color:#000;';
            } else {
                echo 'color:#999;';
            }
            echo 'width:', (strlen($count)) * 10,
             'px;">' , $index ,'</span></div>';
            ++$index;
        }
        echo '</code></td><td ><code style="width:100%;float:left">';
        $index = 1;
        foreach ($lines as $key => $line) {
            if ($index + 9 < $errorLine || $index - 11 > $errorLine) {
                ++$index;
                continue;
            }
            if ($index === $errorLine + 1) {
                echo '<div style="padding-left:10px;background-color:#ff6">';
            } else {
                echo '<div style="padding-left:10px;">';
            }
            if ($line === '') {
                $line = '<br>';
            }
            echo  $line, '</div>';
            ++$index;
        }
        echo '</code></td></tr></table>';
        if ($isError === false || $exception->isFatal() === false) {
            echo '<h2 style="padding-top:10px;margin-top:15px;border-top:1px solid #ddd;width:100%;clear:both;">Stack Trace</h2>';
            if ($isError) {
                $trace =  $exception->getSourceTrace();
            } else {
                $trace =  $exception->getTrace();
            }
    //        $trace = StackTraceFormatter::format($trace, false);
            $index = 0;
            echo '<code style="width:100%"><table id="trace" style="width:100%;border:1px solid #ddd;border-collapse: separate;border-radius: 2px;">';
            //$trace[] = '{main}';
            foreach ($trace as $item) {
                if ($item !== '{main}') {
                    $invocation = StackTraceFormatter::formatInvocation($item);
                } else {
                    $invocation = '{main}';
                }
                echo '<tr style="border:1px solid #ddd;';
                echo '"><td class="box" onclick="this.setAttribute(\'class\', \'frame\');document.getElementById(\'tf-',$index,'\').style.backgroundColor=\'#ff6\'" style="padding:6px;width:1px;font-family:arial;font-size:12px;background:#f5f5f5;vertical-align:middle;text-align:center;"><span style="">#', $index, '</span> <td>';
                echo '<td id="tf-',$index,'" style="border-bottom:1px dotted #ddd;padding:5px;';
                    if ($index === count($trace) -1) {
                        echo 'border:0';
                    }
                echo ';font-family:arial;font-size:14px;">', $invocation;
                if (isset($item['file'])) {
                    $file = $item['file'];
                    if (strncmp($filePrefix, $file, $filePrefixLength) === 0) {
                        $file = substr($file, $filePrefixLength);
                    }
                    $file = str_replace(DIRECTORY_SEPARATOR, '<span style="color:#999;padding:0 3px">' . DIRECTORY_SEPARATOR . '</span>', $file);
                    if ($item === '{main}') {
                        break;
                    }
                    echo '<div style="margin-top:4px;color:#007700;font-size:13px;">', $file , ' <span style="color:#666;background:#f5f5f5;padding:2px 3px 0 3px;line-height:18px;box-shadow: 1px 1px 1px rgba(0,0,0,.15);border-radius:3px;font-size:12px;">',$item['line'], '</span></div>';
                } else {
                    echo '<div style="margin-top:4px;color:#999">internal function</div>';
                }
               echo '</td></tr>';
                ++$index;
            }
            echo '</table></code>';
        }
        echo '</div>';
//        echo '<h2>output</h2>';
//        echo '<h3>headers</h3>';
//        if ($isHeadersSent) {
//            echo '<h4>Already Sent</h4>';
//        }
//        if (count($headers) === 0) {
//            echo '<span style="color:#999;background-color:#eee">NULL</span>';
//        } else {
//            foreach ($headers as $header) {
//                echo $header . '<br>';
//            }
//        }
//        echo '<h3>body</h3>';
//        if ($outputBuffer === false) {
//            echo '<span style="color:red;background-color:#eee">Output Buffer Error</span>';
//        } else {
//            if (strlen($outputBuffer) > 1) {
//                $preview = addslashes($outputBuffer);
//                $preview = str_replace("\n", '\n', $preview);
//                $preview = str_replace("</script>", '<" + "/script>', $preview);
//
//                <h4>[PREVIEW]</h4>
//                <iframe name="buffer" id="buffer" src="javascript:false" width="100%"></iframe>
//                <script>
//                    var preview = window.frames["buffer"].document;
//                    preview.open();
//                    preview.write("<?= $preview ");
//                    preview.close();
//                    document.getElementById("buffer").height = preview.body.scrollHeight + 'px';
//                </script>
//
//                echo '<h4>[SROUCE]</h4>';
//                echo '<pre style="word-break:break-all;word-wrap: break-word;">';
//                echo htmlspecialchars($outputBuffer, ENT_QUOTES | ENT_SUBSTITUTE);
//                echo '</pre>';
//            } else {
//                echo '<span style="color:#999;background-color:#eee">NULL</span>';
//            }
//        }
//        if ($isError) {
//            echo '<h2>context</h2>';
//            $context = $exception->getContext();
//            if ($context === null) {
//                echo '<span style="color:#999;background-color:#eee">NULL</span>';
//            } else {
//                var_dump($exception->getContext());
//            }
//        }
//        echo '<hr /> Powered by Hyperframework';
    }

    private static function toArray($tokens) {
        $highlight = [
            'string'    => ini_get('highlight.string'),
            'comment'   => ini_get('highlight.comment'),
            'keyword'   => ini_get('highlight.keyword'),
            'bg'        => ini_get('highlight.bg'),
            'default'   => ini_get('highlight.default'),
            'html'      => ini_get('highlight.html')
        ];
        $replace = array(
            "\t"    => '&nbsp;&nbsp;&nbsp;&nbsp;',
            ' '     => '&nbsp;'
        );
        $span = '<span style="color: %s;">%s</span>';
        $stringflag = false;
        $i          = 0;
        $out        = array();
        $out[$i]    = '';
        // Loop through each token
        foreach ($tokens as $j => $token) {
            // Single char
            if (is_string($token)) {
                // Entering or leaving a quoted string
                if ($token === '"' && $tokens[$j - 1] !== '\\') {
                    $stringflag = !$stringflag;
                    $out[$i] .= sprintf($span, $highlight['string'], $token);
                } else {
                    // Skip token2color check for speed
                    $out[$i] .= sprintf($span, $highlight['keyword'], htmlspecialchars($token));
 
                    // Heredocs behave strangely
                    list($tb) = isset($tokens[$j - 1]) ? $tokens[$j - 1] : false;
                    if ($tb === T_END_HEREDOC) {
                        $out[++$i] = '';
                    }
                }
                continue;
            }
            // Proper token
            list ($token, $value) = $token;
            // Make the value safe
            $value = htmlspecialchars($value);
            $value = str_replace(
                array_keys($replace),
                array_values($replace),
                $value
            );
            // Process
            if ($value === "\n") {
                // End this line and start the next
                $out[++$i] = '';
            } else {
                // Explode token block
                $lines = explode("\n", $value);
                foreach ($lines as $jj => $line) {
                    $line = trim($line);
                    if ($line !== '') {
                        // Uncomment for debugging
                        //$out[$i] .= token_name($token);
                        // Highlight encased strings
                        $color = ($stringflag === true) ?
                            $highlight['string'] : self::getColor($token, $highlight);
                        $out[$i] .= sprintf($span, $color, $line);
                    }
                    // Start a new line
                    if (isset($lines[$jj + 1])) {
                        $out[++$i] = '';
                    }
                }
            }
        }
        return $out;
    }

    private static function getColor($token, $highlight) {
        switch ($token):
            case T_CONSTANT_ENCAPSED_STRING:
                return $highlight['string'];
                break;
            case T_INLINE_HTML:
                return $highlight['html'];
                break;
            case T_COMMENT:
            case T_DOC_COMMENT:
                return $highlight['comment'];
                break;
            case T_ABSTRACT:
            case T_ARRAY:
            case T_ARRAY_CAST:
            case T_AS:
            case T_BOOLEAN_AND:
            case T_BOOLEAN_OR:
            case T_BOOL_CAST:
            case T_BREAK:
            case T_CASE:
            case T_CATCH:
            case T_CLASS:
            case T_CLONE:
            case T_CONCAT_EQUAL:
            case T_CONTINUE:
            case T_DEFAULT:
            case T_DOUBLE_ARROW:
            case T_DOUBLE_CAST:
            case T_ECHO:
            case T_ELSE:
            case T_ELSEIF:
            case T_EMPTY:
            case T_ENDDECLARE:
            case T_ENDFOR:
            case T_ENDFOREACH:
            case T_ENDIF:
            case T_ENDSWITCH:
            case T_ENDWHILE:
            case T_END_HEREDOC:
            case T_EXIT:
            case T_EXTENDS:
            case T_FINAL:
            case T_FOREACH:
            case T_FUNCTION:
            case T_GLOBAL:
            case T_IF:
            case T_INC:
            case T_INCLUDE:
            case T_INCLUDE_ONCE:
            case T_INSTANCEOF:
            case T_INT_CAST:
            case T_ISSET:
            case T_IS_EQUAL:
            case T_IS_IDENTICAL:
            case T_IS_NOT_IDENTICAL:
            case T_IS_SMALLER_OR_EQUAL:
            case T_NEW:
            case T_OBJECT_CAST:
            case T_OBJECT_OPERATOR:
            case T_PAAMAYIM_NEKUDOTAYIM:
            case T_PRIVATE:
            case T_PROTECTED:
            case T_PUBLIC:
            case T_REQUIRE:
            case T_REQUIRE_ONCE:
            case T_RETURN:
            case T_SL:
            case T_SL_EQUAL:
            case T_SR:
            case T_SR_EQUAL:
            case T_START_HEREDOC:
            case T_STATIC:
            case T_STRING_CAST:
            case T_SWITCH:
            case T_THROW:
            case T_TRY:
            case T_UNSET_CAST:
            case T_VAR:
            case T_WHILE:
            case T_USE:
            case T_NS_SEPARATOR:
                return $highlight['keyword'];
                break;
            case T_CLOSE_TAG:
            case T_OPEN_TAG:
            case T_OPEN_TAG_WITH_ECHO:
            default:
                return $highlight['default'];
 
        endswitch;
    }
}
