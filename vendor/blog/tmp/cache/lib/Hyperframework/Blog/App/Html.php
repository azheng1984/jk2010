<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Web\Html\CssManifestLinkTag;

class Html {
    public function render($ctx) {
        CssManifestLinkTag::render('/main.css'); 
    }
}
