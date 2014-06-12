<?php
namespace Hyperframework\Web;

class ApiApplication extends Application {
    public static function run($path) {
        static::initializePathInfo($path);
        static::executeAction();
        static::renderView();
    }

    protected function renderView() {
        $mediaType = $_SERVER['REQUEST_MEDIA_TYPE'];
        if ($mediaType === null) {
            $mediaType = Config::get(
                'hyperframework.web.api_application.default_media_type'
            );
            if ($mediaType === null) {
                $mediaType = 'json';
            }
        }
        if ($mediaType === 'json') {
            echo json_encode(self::getActionResult());
        }
        throw new NotAcceptableException; 
    }
}
