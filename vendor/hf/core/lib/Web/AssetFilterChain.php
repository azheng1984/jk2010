<?php
namespace Hyperframework\Web;

//todo filter 配置
class AssetFilterChain {
    public static function run($path) {
        $segments = explode('.', $path);
        $content = file_get_contents($path);
        array_shift($segments);
        for (;;) {
            $filterType = array_pop($segments);
            if (self::isValidFilterType($filterType) === false) {
                break;
            }
            if ($filterType === 'php') {
                $content = self::processPhp($content);
            } elseif ($filterType === 'js') {
                $content = self::processjs($content);
            } elseif ($filterType === 'css') {
                $content = self::processCss($content);
            } elseif ($filterType === 'manifest') {
                $content = AssetManifest::process(dirname($path), $content);
            }
        }
        return $content;
    }

    public static function removeInternalFileNameExtensions($path) {
        //path.js.php
        $segments = explode('.', $path);
        $result = array(array_shift($segments));
        for (;;) {
            $filterType = array_pop($segments);
            if ($filterType !== 'php' && $filterType !== 'manifest') {
                array_push($result, $filterType);
                break;
            }
        }
        return implode('.', $result);
    }

    private static function gzip($content) {
    //    $result = gzencode($content, 9);
    //    if ($result === false) {
    //        throw new Exception;
     //   }
     //   header('Content-Encoding: gzip');
        return $content;
    }

    private static function processJs($content) {
        $content = JsCompressor::run($content);
        return self::gzip($content);
    }

    private static function processCss($content) {
        $content = CssCompressor::process($content);
        return self::gzip($content);
    }

    private static function processPhp($content) {
        ob_start();
        eval('?>' . $content);
        return ob_get_clean();
    }

    private static function isValidFilterType($filterType) {
        return $filterType === 'js'
            || $filterType === 'css'
            || $filterType === 'php'
            || $filterType === 'manifest';
    }
}
