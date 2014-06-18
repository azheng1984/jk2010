<?php
namespace Hyperframework\Web;

class ApiApp extends App {
    protected function renderView() {
        if ($this->isViewEnabled() === false) {
            ApiViewDispatcher::run($pathInfo, $this);
        }
            if (ViewDispatcher::tryRun($this->pathInfo, $this) === false) {
                if ($_SERVER['REQUEST_MEDIA_TYPE'] === 'json') {
                    echo json_encode(self::getActionResult());
                }
            }
            return;
        }
        $mediaType = $_SERVER['REQUEST_MEDIA_TYPE'];
        if ($mediaType === null) {
            $mediaType = Config::get(
                'hyperframework.web.default_media_type'
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
