<?php

class Html {
    public static function render($app) {
        FormBuilder::run(
            'article',
            $app->getActionResult('article'),
            $app->getActionResult('errors'),
            Article::getValidationRules()
        );
    }
}
