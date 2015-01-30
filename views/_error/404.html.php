<?php
$this->setLayout('_layouts/main');
$this->setBlock('body', function() {
    var_dump($this['exception']);
    var_dump($this['code']);
    var_dump($this['text']);
    $this->render('_error/share');
    echo "i'm 404 view";
});
