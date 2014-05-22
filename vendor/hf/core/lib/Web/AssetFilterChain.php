<?php
namespace Hyperframework\Web;

//todo filter 配置
class AssetFilterChain {
    private static function process($path) {
        $segments = explode('.', $path);
        $output = file_get_contents($path);
        for (;;) {
            $filterType = array_pop($path);
            if (self::isValidFilterType($fileType) === false) {
                break;
            }
            if ($fileType === 'php') {
                $content = self::processPhp($content);
            } elseif ($fileType === 'js') {
                $content = self::processjs($content);
            } elseif ($fileType === 'css') {
                $content = self::processCss($content);
            }
        }
        return $content;
    }

    public static function removeInternalFileNameExtensions($path) {
        //path.js.php
        //path.js
        $segments = explode('.', $path);
        for (;;) {
            $filterType = array_pop($segments);
            if ($fileType !== 'php') {
                array_push($filterType);
                break;
            }
        }
        return implode('.', $segments);
    }

    private static function gzip($content) {
        $result = gzencode($content, 9);
        if ($result === false) {
            throw new Exception;
        }
        return $result;
    }

    private static function processJs($content) {
        $content = JsCompressor::process($content);
        return self::gzip($content);
    }

    private function processCss($content) {
        $content = CssCompressor::process($content);
        return self::gzip($content);
    }

    private static function processPhp($content) {
        ob_start();
        eval('?>' . $content);
        return ob_get_clean();
    }

    private static function isValidFilterType($fileType) {
        return $fileType === 'js' || $fileType === 'css' || $fileType === 'php';
    }
}
