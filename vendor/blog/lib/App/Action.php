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

class Action {
    public function before() {
    //    throw new \Exception;
        //print_r($_SERVER);
        CsrfProtection::run();
        Logger::info(function() {
            return array('hello' . PHP_EOL . '%s %s', 123, 'hello');
        });
    }

    public function after($ctx) {
        
    }

    public function patch($ctx) {
        echo 'hello';
    }
}
