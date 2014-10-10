<?php
namespace Hyperframework\Blog\Biz;

use Hyperframework\Db\DbTable;
use Hyperframework\Db\DbClient;
use Hyperframework\Blog\Biz\Comment;

class Article extends DbModel {
    public static function getTopLike() {
        $article = new Article;
        $article->row = DbClient::getColumn(
            'SELECT * FROM Article ORDER BY like_count DESC LIMIT 1'
        );
        return $article;
    }

    public static function delete($row) {
        DbTransaction::run(function() use ($row) {
            parent::delete($row);
            DbClient::deleteByColumns('Comment', ['article_id' => $row['id']]);
        });
    }

    public static function isPopular($row) {
        $article = self::getById('xxx');
        $article = self::getCacheById('xxx');
        $article->executeA();

        $row = Article::getTopLike();

        Article::isPopular($row);
        $wrapper = new Wrapper($row);
        template_method($wrapper);
        tm {
            if ($wp['x'] === false) {
                $wp->executeA();
            } else {
                $wp->executeB();
            }
        }
    }
}
