<?php
$this->setLayout('_layouts/main');
$this->setBlock('body', function() {
    var_dump($this['exception']);
    var_dump($this['status_code']);
    $this->render('_error/share');
    echo "i'm 404 view";
});
