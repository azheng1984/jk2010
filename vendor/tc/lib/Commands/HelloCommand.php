<?php
namespace Tc\App;

class HelloCommand {
    public function execute($arg1, $arg2) {
        Context::getOption('header');
        Context::getOptionCount();
        Context::getOptions();
        Context::getArguments();
        Context::getArgumentCount();
        Context::getCount();
        Context::getAll();
        $ctx->getParams();
    }
}
