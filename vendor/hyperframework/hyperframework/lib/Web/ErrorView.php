<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\PathCombiner;
use Hyperframework\Common\FileLoader;

class ErrorView {
    public function render($exception) {
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = '500 Internal Server Error';
        }
        $code = explode(' ', $statusCode, 2)[0];
        $rootPath = Config::getString(
            'hyperframework.error_view.root_path', ''
        );
        if ($rootPath === '') {
            $rootPath = Config::getString(
                'hyperframework.view.root_path', 'views'
            );
            PathCombiner::append($rootPath, '_error');
        }
        if (Config::getBoolean(
            'hyperframework.web.error_view.filename.include_output_format', true
        )) {
            $format = (string)$this->getFormat();
            if ($format === '') {
                $format = self::getFormat();
            }
            $files = [
                $code . '.' . $format . '.php', 'error.' . $format . '.php'
            ];
        } else {
            $files = [$code . '.php', 'error.php'];
        }
        $rootPath = FileLoader::getFullPath($rootPath);
        $path = null;
        foreach ($files as $file) {
            PathCombiner::prepend($file, $rootPath);
            if (file_exists($file)) {
                $path = $file;
                break;
            }
        }
        if ($path === null) {
            header("content-type: text/plain;charset=utf-8");
            echo $statusCode;
        } else {
            $view = new View([
                'exception' => $exception, 'status_code' => $statusCode
            ]);
            $view->render($path);
        }
    }

    protected function getFormat() {
        return Config::getString(
            'hyperframework.web.view.default_output_format', 'html'
        );
    }
}
