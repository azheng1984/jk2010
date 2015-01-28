<?php
namespace Hyperframework\Web;

class ErrorView {
    public function render($exception) {
        $rootPath = Config::getString(
            'hyperframework.error_view.root_path', '_error'
        );
        $statusCode = '';
        $statusDescription = '';
        if ($this->getSource() instanceof HttpException) {
            echo $this->getSource()->getStatusCode();
        } else {
            echo '500 Internal Server Error';
        }
        $pattern = '#\.([0-9a-zA-Z]+)$#';
        $requestPath = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        if (preg_match($pattern, $requestPath, $matches) === 1) {
            $format = $matches[1];
        }
        //search ...
        $view = new View(
            ['exception' => $this->getSource(), 'status_code' => '500']
        );
        $view->render($path);
        header('Content-Type: text/plain;charset=utf-8');
    }
}
