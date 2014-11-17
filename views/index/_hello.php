<?php $this->extend('_hello_layout'); ?>
hello!!!!!!!!!!!
<?php
$this->setBlock('hi', function() {
    echo 'hi block in hi';
});
