<?php
namespace Yxj\Web\Action;

abstract class ArticleDataBindingAction {
    public function before() {
        //check autentication
    }

    protected function bind() {
        if (ArticleDataBinder::bind() !== false) {
            Application::redirect(
                '/article/' . ArticleDataBinder::getId(), 302
            );
        }
    }
}
