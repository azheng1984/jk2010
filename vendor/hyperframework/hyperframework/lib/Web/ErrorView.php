<?php
namespace Hyperframework\Web;

class ErrorView {
    public function render($exception) {
        $rootPath = Config::getString(
            'hyperframework.error_view.root_path', '_error'
        );
        if ($this->getSource() instanceof HttpException) {
            $statusCode = $this->getSource()->getStatusCode();
        } else {
            $statusCode = '500 Internal Server Error';
        }
        $pattern = '#\.([0-9a-zA-Z]+)$#';
        $requestPath = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        if (preg_match($pattern, $requestPath, $matches) === 1) {
            $format = $matches[1];
        } else {
            $format = 'html';//config
        }
        //search ...
        if ($noView) {
            header('Content-Type: text/plain;charset=utf-8');
            echo $statusCode;
       } else {
            $view = new View([
                'exception' => $this->getSource(), 'status_code' => $statusCode
            ]);
            $view->render($path);
        }
    }
}
