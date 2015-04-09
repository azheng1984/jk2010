<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;

class DownloadsController extends Controller {
    public function doShowAction() {
        return ['title' => '下载 - Hyperframework'];
    }
}
