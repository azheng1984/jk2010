<?php
namespace Hyperframework\Web;

class RequestEngine {
    public function getAllHeaders() {
        return getallheaders();
    }

    public function openInputStream() {
        return fopen('php://input');
    }
}
