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

private static function save() {
    $article = FormFilter::run('article');
    //$article = Validator::run($article, array('...'));
    Article::save($article);

    $article = Article::getRowById($id, 'title');

    $comments = Article::getComments($article['id'], 1, 3);
    Artile::getRowById($id);

    $row = DbClient::getRowById('article', $id, 'title');

    $title = DbClient::getColumn(
        'SELECT title From article WHERE id = ?', $id
    );

    Article::getRowById($id, 'count(*)');
    Article::has($id);

    $originalArticle = DbArticle::getRow('*', 'id=' . $article['id']);
    if ($originalArticle === null || $userId = $article['user_id']) {
        DbArticle::save($article);
    }
}

private static function save() {
    $article = FormFilter::execute('article');
    DbArticle::save($article);
}
