<?php
//v3
//module 是否已经是约定俗成？
$this->defineSegment('posts' => [
    'type' => 'collection',
    'children' => 'comments'
]);
return [
    'articles' => [
        'include' => ['commentable', 'postable'], //concern
        'type' => 'collection',
        'item' => [
            'segment_pattern' => '/list/',
            'segment_name' => 'name',
            'children' => [
            ],
        ],
        'excluded_actions' => ['get'],
        'actions' => [''],
        'shallow_nesting' => true,
        'formats' => ['[xml]', 'rss'],
        'callback' => function($ctx) {
            if (in_array($ctx->getAction(), ['edit', 'create'])) {
                $this->setController('ArticlesCommentsController');
            }
            if ($ctx->getDomain(0) !== 'admin') {
                return false;
            }
            if ($ctx->getAction() === 'new') {
                $this->setAction('add');
            }
        },
        'children' => [
        ],
    ],
];

return [
    [
        'get', 'from' => 'asfdsf', 'to' => 'article#create', 'subdomain' => 'admin'
    ],
    [
        'post', 'from' => 'xxx'
    ],
];

$this->match('post', 'asfdsf/:id/:user_id', 'article#create', ['constrains']);
$this->match('[:controller[:action[:id]]]');

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
        function($node) {
            $node->setType('collection');
        },
        'item' => [
            'id_pattern' => '/()+/', //default: ctypenum
            'formats' => ['[xml]'],
            [
                'comments' => [
                    function($node) {
                        $node->addLink(
                        );
                    }
                    'links' => [
                        [
                            'name' => 'ArticlesCommentsController'
                            //'target' => 'articles_comments',
                            'actions' => ['create'],
                        ]
                    ],
                    'actions' => ['create'],
                    'controller' => 'ArticlesCommentsController',
                    'formats' => 'xml'
                    //'actions' => ['delete'],
                ], //'link' to /comments will render comments
            ],
            function($ctx) {
                if (in_array($ctx->getAction(), ['action', 'xxx'])) {
                    $node->setController(
                        'ArticlesCommentsController', ['create']
                    );
                }
            },
        ],
        'formats' => ['xml'],
//      'controller' => 'ArticleController', => code map
    ],
    'search',
    'rss',
    'tags' => [
        'type' => 'collection'
    ],
    // /search?key=sdsf&key2=df
    'xx' => [
        'type' => 'flag',
        'children' => [
        ],
    ],
    'comments' => ['list'],
    'sign_in' => ['children' => 'xxewe'],
];

$this->xx('articles/{}/{}', '');
$this->xx('articles/{}/{}', '');
$this->addLink('articles/comments', []);
$this->addLink('articles/comments', []);
$this->changeController('articles/comments', []);

//  /articles/comments

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
