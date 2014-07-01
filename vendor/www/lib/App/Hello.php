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
        DbClient::deleteById('article', $app->getParam('id'));
    }

    public function get($app) {
        $articleId = $app->getParam('id');
    }

    private static function save() {
        DbClient::save('article', FormFilter::run('article'));
        //InputBinder::save('article');
    }
}

class InputBinder {
    public static function save($config, $dbTableName = null) {
        if ($dbTableName === null) {
            $dbTableName = $config;
        }
        $result = InputFilter::execute($config);
        DbClient::save($result);
    }
}

private static function save() {
    $article = FormFilter::run('article');
    $article = Validator::run($article, array('...'));
    DbClient::save('article', $article);

    $row = DbClient::getRowById('article', $id, 'title');
    $title = $row['title'];

    $title = DbClient::getColumn(
        'SELECT title From article WHERE id = ?', $id
    );

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
