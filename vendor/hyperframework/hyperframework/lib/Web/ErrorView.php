<?php
namespace Hyperframework\Web;

use InvalidArgumentException;
use Hyperframework\Common\Config;
use Hyperframework\Common\PathCombiner;
use Hyperframework\Common\FileLoader;

class ErrorView {
    public function render($error, $outputFormat = null) {
        if (isset($error['code']) === false) {
            throw new InvalidArgumentException(
                "Field 'code' of argument 'error' is missing."
            );
        }
        $rootPath = Config::getString(
            'hyperframework.error_view.root_path', ''
        );
        if ($rootPath === '') {
            $rootPath = Config::getString('hyperframework.view.root_path', '');
            if ($rootPath === '') {
                $rootPath = 'views';
            }
            PathCombiner::append($rootPath, '_error');
        }
        $files = [
            ViewPathBuilder::build($error['code'], $outputFormat),
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
            echo $error['code'];
            if (isset($error['text'])) {
                echo ' ' . $error['text'];
            }
        } else {
            $view = ViewFactory::create($error);
            $view->render($path);
        }
    }
}
