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
            'name.hi', array('hello %s', 'az'), array('happy' => array('life'))
        );
        Logger::info(function() {
            return array('hello!', 'hello' . PHP_EOL . '%s %s', 123, 'hello');
        });
        Logger::info('name', null);
    }

    public function after($ctx) {
        
    }

    public function patch($ctx) {
        echo 'hello';
    }
}
