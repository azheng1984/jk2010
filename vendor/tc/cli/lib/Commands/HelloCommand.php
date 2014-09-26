<?php
namespace Tc\Commands;

class HelloCommand {
//    public function execute($arg1, $arg2 = null) {
//    }
//
//    public function execute(array $options, $arg1, $arg2 = null) {
//        if ($options['article']) {
//        }
//    }
//
    public function execute(array $args) {
        echo 'hi from cmd';
    }
//
//    public function execute(array $options, array $args) {
//        if (isset($options['article'])) {
//        }
// /*
//        if ($options->has('article')) {
//        }
//        if ($options['article'] === null) {
//        }
//        if ($options['article'] !== false) {
//        }
//        $array = $options->getAll();
// */
//    }
//
//    public function execute($options, $arg1, array $args) {
    //    $options->get();
    //    $options->getAll('header');
//    }
//
    //inject_options => false 
    // <arg> <file>...
    // $file => <file>
    // $file = null => [<file>]
    // array $files => <file>...
    // array $files = array() => [<file>...]
    // 2 => '<arg...>' //no 3 => array('<arg...>' => 'xxxx')
    // 自然语言语义
    public function execute(array $options, $arg, array $files = null) {
    }
//
//    public function execute(array $elements) {
//        is_int($key); //argument
//        CommandParser::getElements();
//        CommandParser::getArguments();
//        CommandParser::getOptions();
//        getSubcommandName();
//        getGlobalOptions();
//    }
}
