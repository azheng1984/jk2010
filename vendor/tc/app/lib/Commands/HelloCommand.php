<?php
namespace Tc\Commands;

class HelloCommand extends AbstractCommand {
    private $ctx;

    public function __construct($ctx) {
        $this->ctx = $ctx;
    }

    protected function getContext() {

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
        xxx -y -x -h

            if (isset($options['-x'])) {
            }

            if (isset($options['-y'])) {
                //
            }

            foreach ($this->getOptions() as $key => $value) {
                switch ($option) {
                case '-x':
                    //xx
                    break;
                case '-y':
                    //yy
                    break;
                }
            }

            $result = OptionDispatcher::dispatch(array(
                '-x' => function($value) {
                    OptionDispatcher::dispatch([
                    ]);
                    $this->dispatch([
                    ]);
                    OptionDispatcher::stopDispatch();
                },
                '-y' => function() {
                }
            ));

        $results = $this->dispatchAll([
            '-x' => function() use ($this as $x) {
            },
            '-y' => function() {
                $this->stopDispatch();
                $x = 'xx';
                $this->dispatchAll([]);
            }
        ]);

        foreach ($results as $result) {
            $result['name'];
            $result['return'];
        }
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
