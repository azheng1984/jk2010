<?php
namespace Hyperframework\Web\Html;

class DebugPage {
    public static function render($exception) {
        self::resetOutput();
        print_r($exception);
    }

    protected static function resetOutput() {
        print_r(headers_list());
        header_remove();
        $content = '';
        $obLevel = ob_get_level();
        while ($obLevel > 0) {
            $content .= ob_get_clean();
            --$obLevel;
        }
        echo $content;
    }
}
