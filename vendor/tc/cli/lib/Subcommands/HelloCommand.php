<?php
namespace Tc\App;

class HelloCommand {
    public function execute($arg1, $arg2) {
    }

    public function execute(array $options, $arg1, $arg2 = null) {
        if ($options['article']) {
        }
    }

    public function execute(array $args) {
    }

    public function execute(array $options, array $args) {
        if (isset($options['article'])) {
        }
 /*
        if ($options->has('article')) {
        }
        if ($options['article'] === null) {
        }
        if ($options['article'] !== false) {
        }
        $array = $options->getAll();
 */
    }

    public function execute(array $options, $arg1, array $args) {
    }

    public function execute(array $options) {
    }

    public function execute(array $elements) {
        is_int($key); //argument
        CommandParser::getElements();
        CommandParser::getArguments();
        CommandParser::getOptions();
        getSubcommandName();
        getGlobalOptions();
    }
}
