<?php

class Html {
    public static function render($app) {
        $app->getActionResult('article');
        $app->getActionResult('errors');
        FormBuilder::run('article', $article, $errors);
    }
}
