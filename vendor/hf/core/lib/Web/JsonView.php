<?php
namespace Hyperframework\Web;

class JsonView {
    public function render($ctx) {
        header('application/json');
        echo json_encode($ctx->getActionResult());
    }
}
