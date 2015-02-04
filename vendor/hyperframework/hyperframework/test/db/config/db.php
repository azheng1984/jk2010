<?php
return [
    'dsn' => 'mysql:host=localhost;dbname=test',
    'username' => 'hftest',
    'password' => '123456',
    'options' => [
        PDO::ATTR_EMULATE_PREPARES => false
    ]
];
