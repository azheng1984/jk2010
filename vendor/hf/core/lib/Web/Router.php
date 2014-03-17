<?php
namespace Hyperframework\Routing;

class Router {
    //在统一的地方放置分离后的 path segments，以便 input mapper 调用
    public static function execute($result = null) {
        if ($result === null) {
            //TODO
        }

        $redirectType = HierarchyChecker::check($result['path']); //只在 GET 下进行
        $path = $result['path'];
        if ($redirectType !== null) {
            $path = static::adjustPath($path, $redirectType);
        }

        // http://www.baidu.com/category-rice/add => 404

        //www.baidu.com/jquery.min.js
        // http://www.baidu.com/article-123456/add/ =>
        // http://www.baidu.com/article_list(123456)/add => 404
        // http://www.baidu.com/category(rice)/add => 404
        // http://www.baidu.com/category.rice/add => 404
        // 转变成
        // http://www.baidu.com/article/add/ => 404
        //
        // /article/add segments[0][0] = 1232445;
        //cancel，没有 link 对象，可以通过 Router::getParameters(); 来获取参数，处理则和 get 参数保持一致
        static::initializeLink($path, $result['parameters']);
        if ($redirectType !== null) {
            $tmp = explode('?', $_SERVER['REQUEST_URI'], 2);
            $path = $tmp[0] === $result['path'] ?
                $path : static::adjustPath($tmp[0], $redirectType);
            if (isset($tmp[1])) {
                $queryString = '?' . $tmp[1];
            }
            static::redirect(static::getLocation($path, $queryString));
            return;
        }
        return $path;
    }

    protected static function initializeLink($path, $parameters) {
        //TODO，按照 path 分割，一次初始化多个 link 对象
        $pathInfo = \Hyperframework\Web\PathInfo::get($path);
        if (isset($pathInfo['link']['initialization'])) {
            $pathInfo['link']['class']::initialize($parameters);
            return;
        }
        foreach ($parameters as $parameter) {
            if ($parameter !== null) {
                throw new \Hyperframework\Web\NotFoundException;
            }
        }
    }

    protected static function getLocation($path, $queryString){
        return static::getProtocol($path) . '://' .
            static::getDomain($path) . $path . $queryString;
    }

    protected static function getProtocol() {
        if (isset($_SERVER['HTTPS'])) {
            return 'https';
        }
        return 'http';
    }

    protected static function getDomain() {
        return $_SERVER['HTTP_HOST'];
    }

    protected static function redirect($location) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' || 
            $_SERVER['REQUEST_METHOD'] === 'HEAD') {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $location);
        }
    }

    private static function adjustPath($path, $redirectType) {
        if ($redirectType === HierarchyChecker::FILE) {
            return substr($path, 0, strlen($path) - 1);
        }
        return $path . '/';
    }
}
