<?php
namespace Hyperframework\Blog\Biz;

use Hyperframework\Db\DbTable;
use Hyperframework\Db\DbClient;
use Hyperframework\Blog\Biz\Comment;

class Article extends DbActiveRecord {
    public static function getTopLiked() {
        return static::getByColumns();
        return static::getBySql();
        return static::getAllBySql();
        return static::getAllByColumns();
        return static::getAllByColumns();

        return static::getAllBySql(
            'SELECT * FROM Article ORDER BY like_count DESC LIMIT 1'
        );
    }

    public function delete() {
        DbTransaction::run(function() {
            parent::delete();
            DbClient::deleteByColumns('Comment', ['article_id' => $this['id']]);
        });
    }

    public function isPopular() {
        return $this['view_count'] > 10;
    }
}
