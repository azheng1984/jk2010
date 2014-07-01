<?php

class DbTable {
    public static function findById($id, 'name, title') {
    }

    public static function insert($columns) {
        DbClient::insert(static::getTableName(), $columns);
    }

    public static function update($columns, $where/*, $mixed, ...*/) {
        DbClient::update(static::getTableName(), $columns);
    }

    public static function delete($where/*, $mixed, ...*/) {
        DbClient::delete(static::getTableName(), $columns);
    }

    public static function getTitleById($id) {
        static::findById($id, 'title');
    }
}

DbArticle::insert($array);
DbClient::insert('article', $array);

DbClient::getRow('SELECT * FROM article WHERE id = ?', $id);
DbFindByIdCommand::exec('article', $id);
DbArticle::getRowById($id);

DbClient::getRow('SELECT title FROM article WHERE id = ?', $id);
DbFindByIdCommand::exec('article', $id, 'title');
DbArticle::getRowById($id, 'title');
DbArticle::getTitleById($id);

if (DbSaveCommand::execute('article', $article) === 'created') {
    $articleId = DbClient::getLastInsertedId();
}

DbSaveCommand::execute('article', $article);

try {
    DbClient::beginTransaction();
    DbClient::delete('article', $id);
    DbClient::delete('comment', 'article_id = ?' $id);
    DbClient::commit();
} catch (\Exception $ex) {
    DbClient::rollback();
    throw $ex;
}

DbTransaction::execute(function() {
    DbClient::delete('article', $id);
    DbClient::delete('comment', 'article_id = ?' $id);
    DbClient::delete('article_log', 'article_id = ?' $id);
});

$comments = DbClient::getRow('SELECT * FROM comment WHERE article_id = ?', $id);
$comments = @article.comments;

$article = InputFilter::run('article');
DbSaveCommand::run('article', $article);
$app->redirect('/articles/' . $article['id']);

DbClient::insert('article', array());

DbClient::getRow('SELECT * FROM article WHERE id = ?', $articleId);

DbClient::getRowById('article', $articleId);
DbClient::getRowById('article', $articleId, 'title, create_data');
