<?php
namespace Tc\App;

class HelloCommand {
    public function execute($arg1, $arg2) {
        Context::getOption('header');
        Context::getOptionCount();
        $ctx->getParams();
    }
}
