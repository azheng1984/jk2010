<?php
namespace Hyperframework\Web;

class RequestEngine {
    public function getHeaders() {
        return getallheaders();
    }

    public function openInputStream() {
        return fopen('php://input');
    }
}
