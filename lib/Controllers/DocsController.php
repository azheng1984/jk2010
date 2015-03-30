<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;
use Michelf\Markdown;

class DocsController extends Controller {
    public function doShowAction() {
        $html = Markdown::defaultTransform('');
    }
}