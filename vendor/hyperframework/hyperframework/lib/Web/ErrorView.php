<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\FilePathCombiner;
use Hyperframework\Common\FileFullPathBuilder;

class ErrorView {
    /**
     * @param int $statusCode
     * @param string $statusText
     * @param object $error
     * @param string $outputFormat
     */
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
                $rootPath = 'views' . DIRECTORY_SEPARATOR . '_error';
            } else {
                $rootPath = FilePathCombiner::combine($rootPath, '_error');
            }
        }
        $files = [
            ViewPathBuilder::build($statusCode, $outputFormat),
            ViewPathBuilder::build('error', $outputFormat)
        ];
        $rootPath = FileFullPathBuilder::build($rootPath);
        $path = null;
        foreach ($files as $file) {
            $file = FilePathCombiner::combine($rootPath, $file);
            if (file_exists($file)) {
                $path = $file;
                break;
            }
        }
        if ($path === null) {
            Response::setHeader('Content-Type: text/plain; charset=utf-8');
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
