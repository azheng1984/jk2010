<?php
namespace Yxj\Action;

use \Hyperframework\Web;

abstract class ArticleAction {
    public function before() {
        //check autentication
    }

    protected function bind() {
        $result = Web\DataBinder::bind(
            array(
                'user_name' => array(
                    'max_length' => 10,
                    'min_length' => 6,
                    'is_nullable' => false,
                    'type' => 'alpha & number'
                )
            ),
            'Yxj\Biz\Article',
            true
        );
        if ($result['is_success']) {
            Web\Application::redirect('/article/' . $result['id'], 302);
        }
    }
}
