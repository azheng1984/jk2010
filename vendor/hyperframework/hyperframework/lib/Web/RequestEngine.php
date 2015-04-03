<?php
namespace Hyperframework\Web;

class RequestEngine {
    /**
     * @return string[]
     */
    public function getHeaders() {
        return getallheaders();
    }

    /**
     * @return resource
     */
    public function openInputStream() {
        return fopen('php://input');
    }
}
