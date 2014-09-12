<?php
namespace Tc\App;

class HelloCommand {
    public function __construct($options) {
    }

    public function execute($hi, $hi2 = null) {
        echo $hi;
    }
}
