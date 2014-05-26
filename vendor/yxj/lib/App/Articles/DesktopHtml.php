<?php
namespace Yxj\App\Article;

class Html {
    public function render() {
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
