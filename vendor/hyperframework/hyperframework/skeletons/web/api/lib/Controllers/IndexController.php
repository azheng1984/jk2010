<?php
namespace Controllers;

class IndexController extends Controller {
    public function doShowAction() {
        return ['message' => 'hello world!'];
    }
}
