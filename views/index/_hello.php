<?php $this->setLayout('index/_hello_layout');
$this->setBlock('footer', function() {
    var_dump('block hi');
});
