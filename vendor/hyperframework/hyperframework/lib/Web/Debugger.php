<?php
namespace Hyperframework\Web;

use Exception;

class Debugger {
    public static function execute(
        $exception,
        array $previousErrors = null,
        $headers = null,
        $outputBuffer = null
    ) {
        $isError = !($exception instanceof Exception);
        $isHeadersSent = headers_sent();
        if (headers_sent() === false) {
            header('Content-Type: text/html; charset=UTF-8');
        }
        echo '<h1>* Debug *</h1>';
        echo '<h2>';
        if ($isError) {
        //    if ($exception->isFatal() === false) {
                echo '[', $exception->getTypeAsString(), '] ';
        //    } else {
        //        echo '[Fatal Error] ';
        //    }
        } else {
            echo get_class($exception);
            if ($exception->getMessage() !== '') {
               echo ': ';
            }
        }
        echo $exception->getMessage();
        echo '</h2>';
        if ($exception->getFile() === 'undefined') { //???
            echo '<h3>FILE:</h3>';
            echo '<span style="color:#999;background-color:#eee">undefined</span>';
        } else {
            echo '<h3>FILE: ',$exception->getFile(), '</h3>';
            $sourceCode = highlight_string(
                file_get_contents($exception->getFile()), true
            );//highlight_file 会附带 compile warning
            $lines = explode("<br />", $sourceCode);
            $index = 1;
            $count = count($lines);
            $errorLine = $exception->getLine() - 1;
            //todo 窗口化，窗口大小固定 21，除非文件大小小于 21
            foreach ($lines as $key => &$line) {
                if ($index - 11 > $errorLine || $index + 9 < $errorLine) {
                    if ($key === 0) {
                        $lines[$key] = '<code><span style="color:#000">';
                    } elseif ($key === $count - 1) {
                        $lines[$key] = '</span></code>';
                    } else {
                        //if ($index === 193) {
                        //    echo 193;
                        //}
                    //    echo $key. ' ';
                        unset($lines[$key]);
                    }
                    ++$index;
                    continue;
                }
                $content = $line;
                $line = '<span style="color:#ccc;width:';
                $line .= (strlen($count)) * 10;
                $line .= 'px;display:inline-block">' . $index .'</span> ' . $content;
                ++$index;
            }
            $index = $exception->getLine() - 1;
            $lines[$index - 1] = $lines[$index - 1]
                . '<div style="background-color:#ff6">' . $lines[$index] . '</div>';
            unset($lines[$index]);
            if (isset($lines[$index + 1])) {
                $lines[$index - 1] .= $lines[$index + 1];
                unset($lines[$index + 1]);
            }
            //print_r($lines);
            echo implode("<br />", $lines);
        }
        echo '<h2>stack trace</h2>';
        if ($isError === false || $exception->isRealFatal() === false) {
            $stackTrace = $exception->getTrace();
            //if ($isError) {
            //    array_shift($stackTrace);
            //    array_shift($stackTrace);
            //}
            $index = 0;
            foreach ($stackTrace as $item) {
                $trace = [];
                //if (isset($item['class'])) {
                //    $trace['class'] = $item['class'];
                //}
                if (isset($item['function'])) {
                    $trace['function'] = $item['function'];
                }
                if (isset($item['file'])) {
                    $trace['file'] = $item['file'];
                }
                if (isset($item['line'])) {
                    $trace['line'] = $item['line'];
                }
                echo '<br>#', $index, ' ' , $trace['file'], '(',$trace['line'],'): ', $trace['function'];
                ++$index;
            }
            //echo implode('<br>', explode("\n", $exception->getTraceAsString()));
        } else {
            echo '<span style="color:#999;background-color:#eee">undefined</span>';
        }
        if ($previousErrors !== null) {
            echo '<h2>Previous Errors</h2>';
            var_dump($previousErrors);
        }
        echo '<h2>output</h2>';
        echo '<h3>headers</h3>';
        if ($isHeadersSent) {
            echo '<h4>Already Sent</h4>';
        }
        if (count($headers) === 0) {
            echo '<span style="color:#999;background-color:#eee">empty</span>';
        } else {
            foreach ($headers as $header) {
                echo $header . '<br>';
            }
        }
        echo '<h3>body</h3>';
        if ($outputBuffer === false) {
            echo '<span style="color:red;background-color:#eee">Output Buffer Error</span>';
        } else {
            if (strlen($outputBuffer) > 1) {
                $outputBuffer = addslashes($outputBuffer);
                $outputBuffer = str_replace("\n", '\n', $outputBuffer);
                $outputBuffer = str_replace("</script>", '<" + "/script>', $outputBuffer);
?>
                <h4>[PREVIEW]</h4>
                <iframe name="buffer" id="buffer" src="javascript:false" width="100%"></iframe>
<script>
var preview = window.frames["buffer"].document;
preview.open();
preview.write("<?= $outputBuffer ?>");
preview.close();
document.getElementById("buffer").height = preview.body.scrollHeight + 'px';
</script>
 
<?php

                echo '<h4>[SROUCE]</h4>';
                echo '<pre style="word-break:break-all;word-wrap: break-word;">';
                echo htmlspecialchars($outputBuffer, ENT_QUOTES | ENT_SUBSTITUTE);
                echo '</pre>';
            } else {
                echo '<span style="color:#999;background-color:#eee">empty</span>';
            }
        }
        if ($isError) {
            echo '<h2>context</h2>';
            var_dump($exception->getContext());
        }
        echo '<hr /> Powered by Hyperframework';
    }
}
