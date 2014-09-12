<?php
namespace Tc\App;

class HelloCommand {
    public function execute($hi, $hi2 = null) {
        echo $hi;
    }
}
