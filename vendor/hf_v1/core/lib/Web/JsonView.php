<?php
namespace Hyperframework\Web;

class JsonView {
    public function render($actionResult) {
        header('Content-Type: application/json');
        echo json_encode($actionResult);
    }
}
