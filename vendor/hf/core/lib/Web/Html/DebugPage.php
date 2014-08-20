<?php
namespace Hyperframework\Web\Html;

use ErrorException;
use Hyperframework\ErrorCodeHelper;

class DebugPage {
    public static function render(
        $exception, $headers = null, $outputBuffer = null
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
                echo ErrorCodeHelper::toString($exception->getSeverity()) . ': ';
            } else {
                echo 'Fatal Error: ';
            }
        } else {
            echo get_class($exception);
            if ($exception->getMessage() !== '') {
               echo ': ';
            }
        }
        echo $exception->getMessage();
        echo '</h2>';
        echo '<h3>',$exception->getFile(), '</h3>';
        $sourceCode = highlight_file($exception->getFile(), true);
        $lines = explode("<br />", $sourceCode);
        $index = 1;
        $count = count($lines);
        foreach ($lines as &$line) {
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
        echo '<h2>headers</h2>';
        if ($isHeadersSent) {
            echo '<h3>Already Sent</h3>';
        }
        if (count($headers) === 0) {
            echo '<span style="color:#999;background-color:#eee">EMPTY</span>';
        } else {
            foreach ($headers as $header) {
                echo $header . '<br>';
            }
        }
        if ($isError === false || $exception->getCode() === 0) {
            echo '<h2>stack trace</h2>';
            echo implode('<br>', explode("\n", $exception->getTraceAsString()));
        }
        echo '<h2>output</h2>';
        if (strlen($outputBuffer) > 1) {
            echo htmlspecialchars($outputBuffer);
        } else {
            echo '<span style="color:#999;background-color:#eee">EMPTY</span>';
        }
    }
}
