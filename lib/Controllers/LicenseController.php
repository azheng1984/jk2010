<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;

class LicenseController extends Controller {
    public function doShowAction() {
        return [
            'title' => 'The MIT License - Hyperframework'
        ];
    }
}
