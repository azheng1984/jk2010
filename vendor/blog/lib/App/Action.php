<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Modles\Article;
use Hyperframework\Web\CsrfProtection;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbImportCommand;

class Action {
    public function before() {
        CsrfProtection::run();
        DbImportCommand::run('Article', [array('id' => 7, 'name' => 'xx'), array('id' => 8, 'name' => 'xx')]);
      //  $record = array('id' => 4, 'name' => 'save!!');
      //  DbClient::save('Article', $record);
        print_r(DbClient::getColumnById('Article', 4, 'name'));
    }

    public function after($ctx) {
        echo 'xx';
    }

    public function patch($ctx) {
        echo 'hello';
//        $article = $ctx->getForm('article');
//        if (Article::isValid($article, $errors) === false) {
//            return compact('article', 'errors');
//        }
//        Article::save($article);
//        $ctx->redirect('/articles/' . $article['id']);
    }
}
