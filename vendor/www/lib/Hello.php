<?php
namespace Hft;

class Hello {
    public function before() {
        //todo: security check
    }

    public static function patch() {
        $this->save();
    }

    public static function post() {
        $this->save();
    }

    public function delete($app) {
        DbArticle::delete($app->getParam('id'));
    }

    public function get($app) {
        $articleId = $app->getParam('id');
    }

    private static function save() {
        Db::save('article');
    }
}
