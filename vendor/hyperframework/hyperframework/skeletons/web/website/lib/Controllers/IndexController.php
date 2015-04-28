<?php
namespace Controllers;

use Hyperframework\Web\Controller;

class IndexController extends Controller {
    public function doShowAction() {
        return ['message' => 'hello world!'];
    }
}
