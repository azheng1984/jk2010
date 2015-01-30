<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\PathCombiner;
use Hyperframework\Common\FileLoader;

class ErrorView {
    public function render($exception, $statusCode, $outputFormat = null) {
        $code = explode(' ', $statusCode, 2)[0];
        $rootPath = Config::getString(
            'hyperframework.error_view.root_path', ''
        );
        if ($rootPath === '') {
            $rootPath = Config::getString(
                'hyperframework.view.root_path', ''
            );
            if ($rootPath === '') {
                $rootPath = 'views';
            }
            PathCombiner::append($rootPath, '_error');
        }
        $files = [
            ViewPathBuilder::build($code, $outputFormat),
            ViewPathBuilder::build('error', $outputFormat)
        ];
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
            $view = ViewFactory::create([
                'exception' => $exception, 'status_code' => $statusCode
            ]);
            $view->render($path);
        }
    }
}
