<?php
namespace Yxj\App\Article;

abstract class BaseAction {
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
