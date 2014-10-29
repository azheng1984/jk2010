<?php
namespace Hyperframework\Web;

class JsonView implements IView {
    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function render() {
        header('Content-Type: application/json');
        echo json_encode($this->data);
    }
}
