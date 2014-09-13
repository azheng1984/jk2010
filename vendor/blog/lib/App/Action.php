<?php
namespace Hyperframework\Blog\App;

use Hyperframework\Blog\Modles\Article;
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
        //throw new \PDOException;
        $x = $y;
        //print_r($_SERVER);
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
        WebClient::sendAll(array('http://www.baidu.com/'), function($client, $req, $res) {
        });
        $client = new WebClient;
        $client->setOption('headers', array('Accept'));
        $client->get('http://www.baidu.com', array(
            //'headers' => array(
            //    'x: xxx',
            //    'x' => array('xx', 'xx2', null),
            //),
            CURLINFO_HEADER_OUT=>true
        ));
        print_r($client->getInfo());
        DbClient::getRowById('Article', 2);
    }

    public function after($ctx) {
        
    }

    public function patch($ctx) {
        echo 'hello';
    }
}
