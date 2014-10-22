<?php
namespace Tc\Commands;

class HelloCommand extends AbstractCommand {
    private $ctx;

    public function __construct($ctx) {
        $this->ctx = $ctx;
    }

    protected function getContext() {
        $results = $this->dispatchAll([
            '-x' => function() {
            },
            '-y' => function() {
                $this->stopDispatch();
            }
        ]);
        $result['name'];
        $result['return'];
        foreach ($results as $result) {
        }
    }

//    public function execute($arg1, $arg2 = null) {
//    }
//
//    public function execute(array $options, $arg1, $arg2 = null) {
//        if ($options['article']) {
//        }
//    }
//

    public function execute(array $args) {
        $this->getContext()->dispatchOptions([]);

        $this->dispatch(['-x' => array()]);
        $this->dispatchAll([]);

        $this->getOptions();
        $this->getArguments();
        $this->quit();
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
    // 2 => '<arg>...' //no 3 => array('<arg>' => 'xxxx')
//    public function execute(array $options, $arg, array $files = null) {
//    }
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
