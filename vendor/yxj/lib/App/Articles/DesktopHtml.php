<?php
namespace Yxj\App\Article;

class Html {
    public function render($app) {
        $app->getParam('id');
        //Application::getActionResult();
        //Application::getParam('id');
        $form = HtmlForm($input);
        echo '<form method="GET">';
        echo '<label for="name"></label>'
        echo '<input name="name" ', $form->renderValue('name'), '/>'
        echo '<textarea>';
        $form->renderText('comment');
        echo '</textarea>';
        echo '</form>';
    }

    public function renderStatus() {
    }

    public function renderContent() {
    }
}
