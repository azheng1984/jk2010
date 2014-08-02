<?php
namespace Hyperframework\Blog\ErrorApp;

use Hyperframework\Web\HttpException;

class Html {
    public function render($exception) {
        echo '<h1>';
        if ($exception instanceof HttpException) {
            echo $exception->getCode();
        } else {
            echo '500 internal server error';
        }
        echo '</h1>';
        exit;
    }
}
