<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Web\Html\FormBuilder;
use Hyperframework\Blog\Models\Article;

class Html {
    public function render($ctx) {
        FormBuilder::run(
            $ctx->getActionResult('article'),
            array(
                'base' => 'article',
                'errors' => $ctx->getActionResult('errors'),
                'validation_rules' => Article::getValidationRules()
            )
        );

//        FormBuilder::run(
//            $ctx->getActionResult('article'),
//            array(
//                'base' => 'article',
//                'fields' => array(
//                    'title' => array('type' => 'TextBox'),
//                ),
//                'errors' => $ctx->getActionResult('errors'),
//                'validation_rules' => Article::getValidationRules()
//            )
//        );
    }
}
