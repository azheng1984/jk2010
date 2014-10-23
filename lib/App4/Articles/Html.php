<?php
namespace Hyperframework\Blog\App\Article\Index;

class Html {
    public function render($ctx) {
        echo $ctx->getParam('id');
    }
}
