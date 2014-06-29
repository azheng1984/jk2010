<?php
namespace Hft;

class Hello {
    public static function patch() {
        $this->save();
    }

    public static function post() {
        $this->save();
    }

    public function delete($app) {
        DbArticle::delete($app->getParam(0));
    }

    public function get($app) {
        $articleId = $app->getParam(0);
    }

    private static function save() {
        Db::save('article');
    }
}
