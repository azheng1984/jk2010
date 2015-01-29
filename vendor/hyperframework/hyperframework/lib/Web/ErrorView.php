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
            'hyperframework.error_view.root_path',
            'view' . DIRECTORY_SEPARATOR . '_error'
        );
        $rootPath = FileLoader::getFullPath($rootPath);
        $files = [$code . '.php', 'error.php'];
        $format = $this->getFormat();
        if ($format !== null) {
            $files = array_merge([
                $code . '.' . $format . '.php',
                'error.' . $format . '.php'
            ], $files);
        }
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
        $pattern = '#\.([0-9a-zA-Z]+)$#';
        $requestPath = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        if (preg_match($pattern, $requestPath, $matches) === 1) {
            $format = $matches[1];
        } else {
            $format = 'html';
        }
        return $format;
    }
}
