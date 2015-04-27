<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;

class IndexController extends Controller {
    public function doShowAction() {
        return [
            'title' => 'Hyperframework - 简单、专业的 PHP 框架',
            'top_nav' => 'home'
        ];
    }
}
