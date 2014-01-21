<?php
namespace Yxj\Action;

abstract class ArticleAction {
    public function before() {
        //check autentication
    }

    protected function bind() {
        $binder = new \Hyperframework\Web\DataBinder('Yxj\Biz\Article', array(
            'user_name' => array(
                'max_length' => 10,
                'min_length' => 6,
                'is_nullable' => false,
                'type' => 'alpha & number'
            )
        );
        if ($binder::bind() !== false) {
            \Hyperframework\Web\Application::redirect(
                '/article/' . $binder::getId(), 302
            );
        }
    }
}
