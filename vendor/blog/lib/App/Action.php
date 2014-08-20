<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Modles\Article;
use Hyperframework\Web\CsrfProtection;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbImportCommand;

class Action {
    public function before() {
       CsrfProtection::run();
      // $v = array();
      // for ($i = 15000; $i < 16500; ++$i) {
      //     $v[] = array('id' => $i, 'name' => $i . 'v');
      // }
      // DbImportCommand::run('Article', $v);
      //  $record = array('id' => 4, 'name' => 'save!!');
      //  DbClient::save('Article', $record);
      print_r(DbClient::getColumnByColumns('Article', array('id' => '16499'), 'name'));
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
