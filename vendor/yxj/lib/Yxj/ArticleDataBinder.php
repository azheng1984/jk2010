<?php
namespace Yxj\Action;

use \Hyperframework\Web;

abstract class ArticleDataBinder {
    protected function getInputConfig() {
        return array(
            'user_name' => array(
                'max_length' => 10,
                'min_length' => 6,
                'is_nullable' => false,
                'type' => 'alpha & number'
            )
        );
       // if ($result['is_success']) {
       //     Web\Application::redirect('/article/' . $result['id'], 302);
       // }
    }

    public function bind() {
    }
:w
    :w
    public 
}
