<?php
namespace Hyperframework\Blog\App\Articles\Item;

class Html {
    public function render($ctx) {
        echo $ctx->getParam('id');
    }
}
