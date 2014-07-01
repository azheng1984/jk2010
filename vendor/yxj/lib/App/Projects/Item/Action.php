<?php
class DbArticle {
    public static function find($param) {
        return null;
    }
}

DbClient::getRow('SELECT * FROM article WHERE id = ?', $id);

$id = $article['id'];
unset($article['id']);
DbClient::bind('article', $article, 'id = ?', $id);

DbArticle::getById($id);

$id = $article['id'];
unset($article['id']);
DbSaveCommand::execute('article', array('id' => $id), $article);

DbSaveCommand::save('article', $article);
echo $article['id'];
