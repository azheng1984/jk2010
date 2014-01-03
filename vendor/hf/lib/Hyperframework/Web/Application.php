<?php
namespace Hyperframework\Web;

class Application {
    private static $actionResult;
    private static $isViewEnabled = true;

    public static function run($path = null) {
        $pathInfo = PathInfo::get($path);
        static::executeAction($pathInfo);
        static::renderView($pathInfo);
    }

    public static function enableView() {
        static::$isViewEnabled = true;
    }

    public static function disableView() {
        static::$isViewEnabled = false;
    }

    public static function getActionResult() {
        return static::$actionResult;
    }

    public static function redirect($uri, $statusCode = 301) {
        static::$isViewDisabled = true;
        header('Location: ' . $uri, true, $statusCode);
    }

    public static function reset() {
        static::$actionResult = null;
        static::$isViewEnabled = true;
    }

    protected static function executeAction(
        $pathInfo, $processorClass = 'Hyperframework\Web\ActionProcessor'
    ) {
        $info = null;
        if (isset($pathInfo['action'])) {
            $info = $pathInfo['action'];
            $info['namespace'] = $pathInfo['namespace'];
        }
        $processor = new $processorClass;
        static::$actionResult = $processor->run($info);
    }

    protected static function renderView(
        $pathInfo, $processorClass = 'Hyperframework\Web\ViewProcessor'
    ) {
        if (static::$isViewEnabled === false
            || isset($pathInfo['view']) === false
            || $_SERVER['REQUEST_METHOD'] === 'HEAD') {
            return;
        }
        $info = $pathInfo['view'];
        if (is_string($info)) {
            $info = array('view' => $info);
        }
        $info['namespace'] = $pathInfo['namespace'];
        $processor = new $processorClass;
        $processor->run($info);
    }
}
