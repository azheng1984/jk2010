<?php
namespace Hyperframework\Blog\Biz;

use Hyperframework\Db\DbActiveRecord;
use Hyperframework\Db\DbClient;
use Hyperframework\Blog\Biz\Comment;

class Article extends DbActiveRecord {
    public static function getXxx($id = null) {
        $sql = 'ORDER BY like_count DESC LIMIT 1';
        if ($id === null) {
            return static::getBySql($sql);
        }
        return static::getBySql('WHERE id = ? ' . $sql, $id);
    }

    public function delete() {
        DbTransaction::run(function() {
            parent::delete();
            DbClient::deleteByColumns('Comment', ['article_id' => $this['id']]);
        });
    }

    public function getAuthor() {
        return Author::getById($this['author_id']);
    }

    public function getPictureUrl() {
    }

    public function isPopular() {
        return $this['view_count'] > 10;
    }
}
