<?php

return [
    'articles' => [
        'controller' => 'ActionController', //default
        'type' => 'collection',
        'member_actions' => ['show', 'edit', 'preview'],
        'child_actions' => ['search'],
        'default_action' => 'index', //default
        'children' => ['comments']
    ]
]

return [
    'articles' => [
        'controller' => 'ActionController', //default
        'type' => 'collection',
        'actions' => ['index', 'search'],
        'member' => [
            'actions' => ['show', 'edit', 'preview'],
            'children' => ['comments'],
        ]
    ]
]
