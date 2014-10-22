<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Web\Html\CssManifestLinkTag;
use Hyperframework\Web\Html\FormHelper;

class Html {
    public function render($ctx) {
        CssManifestLinkTag::render('/main.css'); 
        $f = new FormHelper(array('data' => array('hello' => 'hi')));
        $f->begin(array('method' => 'POST'));
        $f->renderText(array('id' => 'hello', 'class' => 'hi'));
        $f->renderCheckBox(array('id' => 'hello', 'value' => 'hi'));
        $f->renderFile(array('id' => 'hello', 'class' => 'hi'));
        $f->renderTextArea(array('id' => 'hello', 'class' => 'hi'));
        $f->renderSelect(array(
            'name' => 'hello',
            ':options' => array(
                'xx',
                'yy',
               array(
                   ':options' => array('hi'), 'label' => 'hello'
               )
            ),
        ));
        $f->end();
    }
}

// /articles/23/comments/23
// /articles/23/comments/
