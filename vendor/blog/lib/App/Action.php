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
        CsrfProtection::run();
        //Logger::info('hello');
        $client = new WebClient();
        $client->get('http://www.baidu.com/s', array(CURLINFO_HEADER_OUT => 'ture'));
        print_r($client->getInfo()); 
    }

    public function after($ctx) {
    }

    public function patch($ctx) {
        echo 'hello';
    }
}
