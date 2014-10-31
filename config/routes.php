<?php

//v2
return [
//todo 分离模块路由
//    'admin' => [
//        'type' => 'module',
//        'children' => ''
//        'controller' => 'Admin\IndexController', //default
//    ],
    // /articles/232323/
    'articles' => [
        'type' => 'collection',
//        'children' => [
//            'comments'
//        ],
        'item' => [
            'id_pattern' => '/()+/', //default: ctypenum
            'children' => 'comments',
            'formats' => ['[xml]'],
        ],
        'formats' => ['xml'],
//      'controller' => 'ArticleController', => code map
    ],
    'search',
    'rss',
    'tags' => [
        'type' => 'collection'
    ],
    /search?key=sdsf&key2=df
    'xx' => [
        'type' => 'flag',
        'children' => [
        ],
    ],
    'comments' => ['list'],
    'sign_in' => ['children' => 'xxewe'],
];

function($segment) {
    if ($segment === 'xx') {
        return $segment;
    }
    return [
        'articles' => function($segment) {
            if ($segment === 'id') {
            }
        },
        'tags' => function($segment) {},
        'sign_in',
    ]
}

//v1
return [
    'articles' => [
        'namespace' => 'Articles', //default
        'type' => 'collection', //singular by default if has member set collection automaticly
        'children' => ['comments' => 'shallow'], //as collection paths when type = collection or has member config
        'additional_children' => ['index' => 'IndexPreview'], //rename children?
        'set_child_name' => ['IndexPreview' => 'index'],
        'additional_children' => ['new' => ['children' => ['preview']]], //postpone
        'removed_children' => ['index_preview'], //postpone
        'root' => 'index', //default, can be configed globally
        'include' => [],
        'item' => [
            'pattern' => function($segment) { //or id constraints?
                //number by default
            },
            'children' => ['show', 'edit', 'new', 'preview'], // rewrite
            'additional_children' => [], // append, 'routing.restful' = true
            'include' => ['comments'],
        ],
    ],
    'console' => [
        'namespace' => 'Console', //default
        'type' => 'collection',
        'member_actions' => ['show', 'edit', 'preview'],
        'actions' => ['search'],
        'default_action' => 'index', //default
        'includes' => [
            'comments' => [
            ],
        ],
        'restful' => false,
    ],
    'comments' => ['belongs_to' => ['articles']],
];
