<?php
namespace Hyperframework\Web;

class ApiViewDispatcher extends ViewDispatcher {
    public static function run($pathInfo, $ctx) {
        $class = static::getViewClass($pathInfo);
        if ($class === null) {
            if (isset($_SERVER['REQUEST_MEDIA_TYPE'])
                && $_SERVER['REQUEST_MEDIA_TYPE'] === 'json'
            ) {
                echo json_encode(self::getActionResult());
                return;
            }
            throw new NotAcceptableException;
        }
        static::dispatch($class, $app);
    }
}

class ApiViewDispatcher extends ViewDispatcher {
    public static function getViewClass($pathInfo) {
        $class = parent::getViewClass($pathInfo);
        if ($class !== null) {
            return $class;
        }
        if (isset($_SERVER['REQUEST_MEDIA_TYPE'])
            && $_SERVER['REQUEST_MEDIA_TYPE'] === 'json'
        ) {
            return 'JsonView';
        }
    }
}
