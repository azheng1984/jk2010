<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Db\DbArticle;
use Hyperframework\Web\CsrfProtection;
use Hyperframework\Db\DbClient;
use Hyperframework\Db\DbImportCommand;
use Hyperframework\Db\DbProfiler;
use Hyperframework\WebClient;
use Hyperframework\Logger;
use PDO;

//throw new \Exception;
class Action {
    public function before() {
        //throw new hi('hhi', null);
        //print_r($_SERVER);
        var_dump(\Hyperframework\Blog\Db\DbArticle::count());
        var_dump(\Hyperframework\Db\DbClient::inTransaction());

        $row = DbArticle::getById(100);


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

    public function after($ctx) {
    }

    public function patch($ctx) {
        echo 'hello';
    }
}
