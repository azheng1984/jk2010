<?php
namespace Hyperframework\Web\Html;

class DebugPage {
    public static function render(
        $exception, $headers = null, $outputBuffer = null
    ) {
        echo '<h1>BEBUG</h1>';
        print_r($exception);
    }
}
