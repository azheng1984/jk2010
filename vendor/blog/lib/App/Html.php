<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Web\Html\FormBuilder;
use Hyperframework\Blog\Models\Article;

class Html {
    public function render($ctx) {
        FormBuilder::run(
            $ctx->getActionResult('article'),
            array(
                'import' => 'article',
                'fields' => array(
                    'title' => array('type' => 'TextBox'),
                    'body' => array('type' => 'TextArea'),
                ),
                'errors' => $ctx->getActionResult('errors'),
                'validation_rules' => Article::getValidationRules()
            )
        );
    }
}
