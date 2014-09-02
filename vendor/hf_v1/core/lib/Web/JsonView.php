<?php
namespace Hyperframework\Web;

class JsonView {
    public function render($ctx) {
        header('Content-Type: application/json');
        echo json_encode($ctx->getActionResult());
    }
}
