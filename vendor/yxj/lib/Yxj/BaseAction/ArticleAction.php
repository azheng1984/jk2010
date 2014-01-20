<?php
namespace Yxj\BaseAction;

abstract class ArticleAction {
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
