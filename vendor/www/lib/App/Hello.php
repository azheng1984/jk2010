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

        DbClient::delete('article', 'id = ?', $app->getParam('id'));
        DbClient::delete('command', 'article_id = ?', $app->getParam('id'));

        DbArticle::deleteById($app->getParam('id'));
        DbDeleteByIdCommand::execute('article', $app->getParam('id'));
    }

    public function get($app) {
        $articleId = $app->getParam('id');
    }

    private static function save() {
        InputBinder::save('article');
    }
}

class InputBinder {
    public static function save($config, $dbTableName = null) {
        if ($dbTableName === null) {
            $dbTableName = $config;
        }
        $result = InputFilter::execute($config);
        Db::save($result);
    }
}

private static function save() {
    $article = FormFilter::execute('article');
    $article = Validator::execute($article, array('...'));
    $originalArticle = DbArticle::getRow('*', 'id=' . $article['id']);
    if ($originalArticle === null || $userId = $article['user_id']) {
        DbArticle::save($article);
    }
}

private static function save() {
    $article = FormFilter::execute('article');
    DbArticle::save($article);
}

final class DbArticle extends DbTable {
    protected static function getTableName() {
        return 'article';
    }
}
