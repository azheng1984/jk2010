<?php
namespace Yxj\Action;

use \Hyperframework\Web\PageNotFoundException; 

abstract class ArticleAction {
    public function before() {
        //check autentication
    }

    protected function bind() {
        $result = new \Hyperframework\Web\DataBinder::bind(
            'Yxj\Biz\Article',
            array(
                'user_name' => array(
                    'max_length' => 10,
                    'min_length' => 6,
                    'is_nullable' => false,
                    'type' => 'alpha & number'
                )
            ),
            'id'
        );
        if ($result['success']) {
            \Hyperframework\Web\Application::redirect(
                '/article/' . $result['id'], 302
            );
            return;
        }
        throw new PageNotFoundException;
    }
}
