<?php
class ArticleForm extends \Hyperframework\Form {
    private static $bindingResult;

    public static function bind() {
        $result = DataBinder::bind('field config ...');
        static::$bindingResult = $result;
        if ($result['is_success']) {
            DbArticle::bind($result['data']);
        }
        return $result['is_success'];
    }

    public static function render() {
    }
}
