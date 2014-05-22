<?php
namespace Hyperframework\Web;

class AssetFilterChain {
    private static function process($path) {
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

    public static function removeFilterExtensions($path) {
    }

    private static function filter($content, $fileType) {
        if ($fileType === 'php') {
            ob_start();
            eval('?>' . $content);
            return ob_get_clean();
        }
        return $content;
    }

    private static function isValidFilterTypes($path) {
        return null;
    }
}
