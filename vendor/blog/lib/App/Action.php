<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Modles\Article;
use Hyperframework\Web\CsrfProtection;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbImportCommand;
use PDO;

class Action {
    public function before() {
        CsrfProtection::run();
        // $v = array();
        // for ($i = 15000; $i < 16500; ++$i) {
        //     $v[] = array('id' => $i, 'name' => $i . 'v');
        // }
        // DbImportCommand::run('Article', $v);
//      $record = array('id' => 1001, 'name' => 'save!!');
//      DbClient::save('Article', $record);
        $s = DbClient::prepare('select * from Article where id = 8', array(
            PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC));
        $s->setFetchMode(PDO::FETCH_ASSOC);
        //$s->setAttribute();
//        $s->bindValue(1, 1);
        $s->execute();
        print_r($s->fetchAll(null));
        //var_dump($s->fetch(PDO::FETCH_OBJ));
        //$s->nextRowset();
        //var_dump($s->fetch(PDO::FETCH_ASSOC));
        //$stat->debugDumpParams();
        //DbClient::getAll('select * from Article');
        //print_r(DbClient::getColumnByColumns('Article', array('id' => '4'), 'name'));
    }

    public function after($ctx) {
        //echo 'xx';
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
