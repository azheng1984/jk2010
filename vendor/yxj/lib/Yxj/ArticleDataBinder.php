<?php
namespace Yxj\DataBinder;

class ArticleDataBinder {
    protected function getInstance() {
        return 'instance';
    }

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
        if (isset($data['id'])) {
            //insert
            return;
        }
        //update
    }
}
