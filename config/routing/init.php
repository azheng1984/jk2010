<?php
//v3
$router->enableModule();

//module 是否已经是约定俗成？
$this->defineSegment('posts' => [
    'type' => 'collection',
    'children' => 'comments'
]);

$this->setDefaultModule('Main'); //default
return [
    'admin' => [
        'root' => 'index',
    ],
];
$this->setDefaultRoot('index');
