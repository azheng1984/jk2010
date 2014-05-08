<?php
namespace Hyperframework\Web;

class AssetProxy {
    public static function run() {
        header('Cache-Control: private, max-age=0, must-revalidate');
        $url = $_SERVER['REQUEST_URI'];
        $assetCacheVersionEnabled =
            Config::get(__NAMESPACE__ . '\AssetCacheVersionEnabled');
        if ($assetCacheVersionEnabled !== false) {
            $segments = explode('.', $url);
            if (count($segments === 1) {
                $segments[] = explode('-', $url);
                array_pop($segments);
                $url = implode('-', $segments);
            } else {
                $extension = array_pop($segments);
                array_pop($segments);
                $url = implode('.', $segments) . '.' . $extension;
            }
            //no expire
        } else {
            header('Cache-Control: private, max-age=0, must-revalidate');
        }
        $pathPrefix = array_shift($segments);
        if (static::startsWith($uri, '/assets/js/')) {
            $assetPath = \Hyperframework\Config::get('Hyperframework\AppPath');
            //$tmp = explode('/', $filePath);
            //array_pop($tmp);
            //$dirName = implode('/', $tmp);
            $search = $assetPath . $pathPrefix . '.' . $segments[0];
            $result = glob($search . '*.js');
            if (count($result) === 0) {
                throw new Exceptions\NotFoundException;
                return;
            }
            if ($result[0] === $search) {
                echo file_get_contents(\Hyperframework\Config::get('Hyperframework\AppPath') . $uri);
                return;
            }
            $suffix = substr($result[0], strlen($search));
            foreach (explode('.', $suffix) as $segment) {
                if ($segment === 'php') {
                    ob_start();
                    require $result[0];
                    $js = ob_get_contents();
                    ob_clean();
                    echo $js;
                }
            }
        } else {
            static::renderVendor($uri);
        }
        //Todo call pipline processor by extension & config
    }

    private static function renderVendor($uri) {
        //abc-12.3.1-1234.js
        //abc-11.js.gz
        //abc-12.js
        //abc-12.js.gz
        //../tv/css/abc.css.less.php
        //export => /tv/css/abc.css => merge + import => deploy
        //=> /tv/css/abc.css
        if (strpos($uri, '/js/tv/') === 0) {
            $path = \Hyperframework\Config::get('Hyperframework\AppPath')
                . '/asset/vendor/tv/public' . $uri;
            if (file_exists($path)) {
                require $path;
                return;
            }
        }
        throw new Exceptions\NotFoundException;
    }
}
