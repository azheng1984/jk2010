<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\PathCombiner;
use Hyperframework\Common\FileLoader;

class ErrorView {
    public function render(
        $statusCode, $statusText, $error, $outputFormat = null
    ) {
        $rootPath = Config::getString(
            'hyperframework.web.error_view.root_path', ''
        );
        if ($rootPath === '') {
            $rootPath = Config::getString(
                'hyperframework.web.view.root_path', ''
            );
            if ($rootPath === '') {
                $rootPath = 'views';
            }
            PathCombiner::append($rootPath, '_error');
        }
        $files = [
            ViewPathBuilder::build($statusCode, $outputFormat),
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
            ResponseHeaderHelper::setHeader(
                'content-type: text/plain; charset=utf-8'
            );
            echo $statusCode;
            if ((string)$statusText !== '') {
                echo ' ' . $statusText;
            }
        } else {
            $view = ViewFactory::createView([
                'status_code' => $statusCode,
                'status_text' => $statusText,
                'error' => $error
            ]);
            $view->render($path);
        }
    }
}
