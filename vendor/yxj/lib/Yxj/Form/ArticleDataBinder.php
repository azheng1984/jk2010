<?php
class ArticleFormBinder extends \Hyperframework\Web\DataBinder {
    protected function getModel() {
        return 'Biz\Article';
    }

    protected function getFields() {
        return array(
            'user_name' => array(
            'max_length' => 10,
            'min_length' => 6,
            'is_nullable' => false,
            'type' => 'alpnum'
        );
    }
}
