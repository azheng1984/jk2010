<?php
namespace Hyperframework\Web;

class ApiViewDispatcher extends ViewDispatcher {
    public static function run($pathInfo, $app) {
        $class = static::getViewClass($pathInfo);
        if ($class === null) {
            if ($_SERVER['REQUEST_MEDIA_TYPE'] === 'json') {
                echo json_encode(self::getActionResult());
                return;
            }
            throw new NotAcceptableException;
        }
        static::dispatch($class, $app);
    }
}
