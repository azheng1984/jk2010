<?php
namespace Hyperframework\Web;

class AssetFilterChain {
    private static function render($path) {
        $segments = explode('.', $path);
        $output = file_get_contents($path);
        for (;;) {
            $filterType = array_pop($path);
            if (self::isValidFilterType($fileType) === false) {
                return;
            }
            $content = self::filter($content, $fileType);
        }
    }

    private static function filter($content, $fileType) {
        if ($fileType === 'php') {
            ob_start();
            eval('?>' . $content);
            return ob_get_clean();
        }
        return $content;
    }
}
