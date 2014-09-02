<?php
namespace Hyperframework\Web\Html;

use ErrorException;
use Hyperframework\ErrorCodeHelper;

class DebugPage {
    public static function render(
        $exception,
        $ignoredErrors = null,
        $headers = null,
        $outputBuffer = null
    ) {
        $isError = $exception instanceof ErrorException;
        $isHeadersSent = headers_sent();
        if (headers_sent() === false) {
            header('Content-Type: text/html; charset=UTF-8');
        }
        echo '<h1>* Debug *</h1>';
        echo '<h2>';
        if ($isError) {
            if ($exception->getCode() === 0) {
                echo '[', ErrorCodeHelper::toString($exception->getSeverity()), '] ';
            } else {
                echo '[Fatal Error] ';
            }
        } else {
            echo get_class($exception);
            if ($exception->getMessage() !== '') {
               echo ': ';
            }
        }
        echo $exception->getMessage();
        echo '</h2>';
        if ($exception->getFile() === 'Unknown') {
            echo '<h3>FILE:</h3>';
            echo '<span style="color:#999;background-color:#eee">UNKNOWN</span>';
        } else {
            echo '<h3>FILE: ',$exception->getFile(), '</h3>';
            $sourceCode = highlight_file($exception->getFile(), true);
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
                . '<div style="background-color:#ff6">' . $lines[$index] . '</div>'
                . $lines[$index + 1];
            unset($lines[$index]);
            unset($lines[$index + 1]);
            echo implode("<br />", $lines);
        }
        echo '<h2>stack trace</h2>';
        if ($isError === false || $exception->getCode() === 0) {
            echo implode('<br>', explode("\n", $exception->getTraceAsString()));
        } else {
            echo '<span style="color:#999;background-color:#eee">UNAVAILABLE</span>';
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
            echo '<span style="color:#999;background-color:#eee">EMPTY</span>';
        } else {
            foreach ($headers as $header) {
                echo $header . '<br>';
            }
        }
        echo '<h3>body</h3>';
        if (strlen($outputBuffer) > 1) {
            //var_dump(mb_detect_encoding($outputBuffer));
            echo htmlspecialchars($outputBuffer, ENT_QUOTES | ENT_SUBSTITUTE);
        } else {
            echo '<span style="color:#999;background-color:#eee">EMPTY</span>';
        }
        echo '<hr /> Powered by Hyperframework';
    }
}
