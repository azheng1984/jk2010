<?php
namespace Hyperframework\Web;

use Exception;
use ErrorException;

class Debugger {
    public static function execute(
        $exception, $headers = null, $outputBuffer = null
    ) {
        $isError = $exception instanceof ErrorException;
        $isHeadersSent = headers_sent();
        if (headers_sent() === false) {
            header('Content-Type: text/html; charset=UTF-8');
        }
        echo '<div style="background:#f6f6f6">';
        echo '<h2 style="font-size:20px;color:black;padding:15px 10px;font-weight:normal;margin:0 5px 0">';
        if ($isError) {
            echo '<span style="color:white;font-family:Arial;border-radius:5px;font-size:13px;red;background:red;padding:5px 7px;">';
            if ($exception->shouldThrow() === true) {
                echo 'Error Exception';
            } elseif ($exception->getSeverityAsString() === 'error') {
                echo 'Fatal Error';
            } else {
                echo ucwords($exception->getSeverityAsString());
            }
            echo '</span> ';
        } else {
            echo get_class($exception);
            if ($exception->getMessage() !== '') {
                echo ': ';
            }
        }
        echo $exception->getMessage();
        echo '</h2>';
        echo '<div style="line-height:20px;color:#ccc;padding:5px 0 5px 15px;font-size:13px;border-bottom:1px solid  #888">';
        echo '<style>body{margin:0;padding:0;}.tab {background:#e1e1e1;border-bottom:1px solid;border-color:#f6f6f6;font-family:Arial;color:#666;padding:5px 25px;margin:5px 1px;}</style>';
        echo '<span class="tab" style="background:#888;border:1px 0;border:solid #888;color:white;text-decoration:none"><b>Code</b></span>';
        echo '<span class="tab">Preview</span>';
        echo '<span class="tab">Raw</span>';
        echo '<span class="tab">Headers</span>';
        echo '</div>';
        echo '</div>';
        $firstLinePrefix = null;
        echo '<h3>File: ',$exception->getFile(), '</h3>';
        $lines = self::toArray((token_get_all(file_get_contents($exception->getFile()))));
        $index = 1;
        $count = count($lines);
        $errorLine = $exception->getLine() - 1;
        echo '<code>';
        foreach ($lines as $key => $line) {
            if ($index + 9 < $errorLine || $index - 11 > $errorLine) {
                ++$index;
                continue;
            }
            if ($index === $errorLine + 1) {
                echo '<div style="background-color:#ff6">';
                echo '<span style="color:#666;';
            } else {
                echo '<span style="color:#bbb;';
            }
            echo 'width:', (strlen($count)) * 10,
             'px;display:inline-block">' , $index ,'</span> ',
            ' ';
            echo  $line;
            if ($index === $errorLine + 1) {
               echo  '</div>';
            } else {
                echo '<br>';
            }
            ++$index;
        }
        echo '</code>';
        echo '<h2>Stack Trace</h2>';
        if ($isError === false || $exception->isFatal() === false) {
            if ($isError) {
                echo implode('<br>', explode("\n", $exception->getSourceTraceAsString()));
            } else {
                echo implode('<br>', explode("\n", $exception->getTraceAsString()));
            }
        } else {
            echo '<span style="color:#999;background-color:#eee">NULL</span>';
        }
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
