<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Biz\Article;
use Hyperframework\Web\CsrfProtection;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbContext;
use Hyperframework\Db\DbImportCommand;
use Hyperframework\Db\DbProfiler;
use Hyperframework\WebClient;
use Hyperframework\Logger;
use PDO;

//throw new \Exception;
class Article extends Controller {
    public function before() {
//      print_r($_SERVER);
//      var_dump(DbClient::beginTransaction());
//      var_dump(Article::count());
//      var_dump(DbClient::inTransaction());
        $article = Article::findById(1);
        var_dump(Article::findAll('name LIKE ? limit 2', '%x%'));
        $name = 'x';
        Article::findAll(['name' => $name]);
        Article::findAll('name = :name', array(':name' => $name));
        var_dump(Article::find('name LIKE ?', '%x%'));
        //Article::getBySql('where name like "%d"');
        if ($article !== null) {
            var_dump($article->getRow()['name']);
        }
        CsrfProtection::run();
        Logger::info(
            'name.hi', array('hello %s', 'az'), array('happy' => array("l\ni\n\nfe\n"))
        );
        Logger::info(
            'name.hi', array("\n"), array('happy' => array("\n"))
        );
        Logger::info(function() {
            return array('hello', 'hello' . PHP_EOL . '%s %s', 123, 'hello');
        });
        Logger::info('name.xx', null, array('hi`~~`'));
        WebClient::sendAll(array('http://www.baidu.com/'), function($client, $req, $res) {});
    }

    public function update() {
    }

    public function show() {
    }

    public function delete() {
    }

    public function add() {
    }

    public function get() {
    }

    public function after($ctx) {
    }

    public function patch($ctx) {
        echo 'hello';
    }
}
