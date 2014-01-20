<?php
namespace Yxj\Action;

abstract class ArticleAction {
    public function before() {
        //check autentication
    }

    protected function bind() {
        $binder = new \Hyperframework\Web\DataBinder('Biz\Article', array(
            'user_name' => array(
                'max_length' => 10,
                'min_length' => 6,
                'is_nullable' => false,
                'type' => 'alpha&num'
            )
        );
        if ($binder::bind() !== false) {
            \Hyperframework\Web\Application::redirect(
                '/article/' . $binder::getId(), 302
            );
        }
    }
}
