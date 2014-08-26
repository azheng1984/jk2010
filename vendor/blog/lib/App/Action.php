<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Modles\Article;
use Hyperframework\Web\CsrfProtection;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbImportCommand;
use Hyperframework\Db\DbProfiler;
use Hyperframework\WebClient;
use PDO;

class Action {
    public function before() {
        CsrfProtection::run();
        // $v = array();
        // for ($i = 15000; $i < 16500; ++$i) {
        //     $v[] = array('id' => $i, 'name' => $i . 'v');
        // }
        // DbImportCommand::run('Article', $v);
      //$record = array('string' =>iconv('utf-8','gb2312', '巍峨哦'), 'date' => '2011-12-12', 'float' => '23.2');
      //DbClient::save('bin_test', $record);
       // $s = DbClient::prepare(
       //     'select * from bin_test order by id desc limit 1'
       // );
       // $s->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        //$s->setAttribute();
       // $param = null;//1000;
       // $s->execute();
        //$s->bindColumn(2, $param);
        //var_dump($s->getColumnMeta(0));
        //var_dump($s->fetchAll());
        //var_dump($param);
        //var_dump($s->fetch(PDO::FETCH_OBJ));
        //$s->nextRowset();
        //var_dump($s->fetch(PDO::FETCH_ASSOC));
        //$stat->debugDumpParams();
//        DbClient::getAll('select * from Article');
//       print_r(DbClient::getColumnByColumns('Article', array('id' => '4'), 'name'));
//        var_dump(DbProfiler::getProfiles());
        $client = new WebClient;
        //var_dump($client->get('http://www.baidu.com/'));
        //var_dump($client->getInfo());
        var_dump(WebClient::sendAll(array('http://www.baidu.com/')));
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
