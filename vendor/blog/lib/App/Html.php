<?php
namespace Hyperframework\Blog\App\Comments;

class Html {
    public static function render($ctx) {
        FormBuilder::run(
            'article',
            $ctx->getActionResult('article'),
            $ctx->getActionResult('errors'),
            Article::getValidationRules()
        );
    }
}
