<?php
namespace Hyperframework\Cli;

use Hyperframework\Config;
use Hyperframework\ConfigFileLoader;
use Hyperframework\Cli\CommandParser;

class App {
    public function run() {
        $args = $_SERVER['argv'];
        array_shift($args);
        $isCollection =
            Config::get('hyperframework.cli.command_collection.enable') === true;
        $configPath = $isCollection ? 'command_collection.php' : 'command.php';
        if ($isCollection) {
            $collectionConfig =
                ConfigFileLoader::loadPhp('command_collection.php');
        }
        $article = $article['comments']->select()->limit(3)->getAll();
        Article::select()->limit(3);
        Article::select()->where(['id' => 3]);
        Article::select()->order('name');
        Article::select()->group('name');
        $article['comments']->select()->group('name');
        $article['comments']->select()->group('name');
        $article['comments']->select()->group('name');
        $article = Article::select(new Query('all', 'limit' => 3))->limit(3)->getAll();
        //execute collection
    }

    public function executeCollection() {
    }
}
