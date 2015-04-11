<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;
use Hyperframework\Db\DbClient;

class BlogController extends Controller {
    public function doShowAction() {
        return ['title' => '日志 - Hyperframework'];
    }
}
