<?php
namespace Hyperframework\Web\Html;

use ErrorException;
use Hyperframework\Common\ErrorCodeHelper;

class Debugger {
    public static function execute(
        $exception,
        $ignoredErrors = null,
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
        if ($ignoredErrors !== null) {
            echo '<h2>ignored errors</h2>';
            var_dump($ignoredErrors);
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
        if (strlen($outputBuffer) > 1) {
            echo '<pre>';
            //var_dump(mb_detect_encoding($outputBuffer));
            echo htmlspecialchars($outputBuffer, ENT_QUOTES | ENT_SUBSTITUTE);
            echo '</pre>';
        } else {
            echo '<span style="color:#999;background-color:#eee">empty</span>';
        }
        echo '<hr /> Powered by Hyperframework';
    }
}
