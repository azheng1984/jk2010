<?php
//v3
//module 是否已经是约定俗成？
$this->defineSegment('posts' => [
    'type' => 'collection',
    'children' => 'comments'
]);
if ($segment[0] === '/') {
    return 'index#show';
}
if (in_array($segment[0], ['login'])) {
}

$this->setRoot('index'); //default
$this->addRoutes(
    ['get', '/search(/*query)', ['constrains' => ('query')]],
);

$this->setDefaultModule('main');
$this->disableRestful();

if ($this->getDomain(3) === 'admin') {
    $this->setModule('admin', ['namespace' => 'Admin']);
    if ($this->getModule() === 'admin') {
        $this->setModuleNamespace('Admin'); //default
    }
    return false; //throw not found exception;
}

if ($this->matchByRegex(
    'get', '(?<controller>.*?)/(?<action>.*?)/$',
    'callback' => function() {
    }
)) {
}
if ($this->match('(:country/):module/:controller/:action/:id(/prefix:year:month{:day}postfix)', ['params' => ''])) {
    $app->setParam('id', $result[0]);
    $this->setPath('search');
    return;
}
//单复数分离，单数接收字符串，复数只能接收数组 cancel，优先考虑使用复数（一致）
//表示集合
if ($this->match('get', 'search/*query', [//get is default method
    ':query' => ['ctype' => 'alnum'], //'\d+' // default regex, cancel 
    ':id' => 'prod-[0-9]{10}', //可以通过在匹配后处理来提高效率，因为不需要执行后期的匹配操作
    'formats' => ['default' => 'rss', 'xml'], //same as default routing
    // option method config or method argument is conflict
    'methods' => ['get' => 'show', 'post' => 'create'],
    //'protocol' => 'https', //postpone, 简单的是简单的，复杂的是可能的，全部放入 extra
    //'port' => '80', //postpone
    //'subdomain' => 'user', //postpone
    'extra' => function($ctx) {
        if (preg_match($query)) {
        }
        return true;
    },
    $this->setParam('xx', 'xx');
])) {
    explode('-', $app->getParam('mixed_params'));
    if ($app->getParam('format')) {
    }
    $this->setPath('search');
    $app->redirect('/xxxx');
    //$this->setSegments(['search']);
    $app->setParam('query', $this->getSegment[1]);

    $this->setController('article');
    $this->setAction('new');

    return;
    return 'search';
    return 'main/search';
    return 'main/search#show';

    return false; //throw new not_found exception

} elseif ($this->segment[0]) {
}

if ($this->parse([
    'index',
    'articles' => function($ctx) {
        if (isset($ctx[0]) === false) {
            $ctx[':controller'] = 'articles';
            return;
        }
        if (isId($ctx[0])) {
            return $ctx->next(function() {});
        }
    }
])) {
    return;
}
if ($this->) {
    return;
}

$router->setRoot(null);
$router->disableRestfulActionConvension();
$router->disableShowAndIndexActionConvension();

$this->setSegment('article', [
    'index' => function() {
    }
]);

$this->Path([
    'index',
    'articles' => function() {
        $this->dispatch()
    },
]);

//todo add extra default action like edit new update...

//  /articles/:id => articles#show

//v5
if ($this->matchResources('articles', [
    'id_pattern' => '[0-9]+',
    'excluded_actions' => '',
    'extra_actions' => '',
    'actions' => '',
    'formats' => [],
    'extra' => function($ctx) {
        $this->fail(); //throw not found exception
    }
])) {
    return;
}
if ($this->matchScope('articles/:article_id', function() {
    if ($this->matchGet('setting(/:action)')) {
        return;
    }
    if ($this->matchResource('comments')) {
        return;
    }
})) {
    return;
}
if ($this->matchResources('articles/:article_id/comments', [
    'formats' => [],
])) {
    return;
}
if ($this->matchResource('account', [
])) {
    return;
}
if ($this->matchResource('account/setting', [
])) {
}
if ($this->match('articles/:article_id/comments/layout(/:xxx)', [
])) {
    return;
}

//v4
return [
    'articles' => [
        'children' => [
        ],
        'item' => [
            'id_pattern' => '[0-9]+', //default
            //'name' => 'article_id', //default
            'formats' => '',
            'shallow_nesting' => true, //default
            'children' => [
                'comments' [
                    'item' => [],
                    'children' => ['reply'],
                ],
                'sigle',
            ],
            'this' => '',
        ],
        'formats' => ['default' => 'xml', 'html'],
        'this' => function() {
        }
    ],
];


//v3
//next
//if some value is setted(:action / :controller), value will be overwrite
return [
    'index',
    'articles' => [
//     'include' => ['commentable', 'postable'], //concern, shared config - postpone
       'type' => 'collection',
       'item' => [
           'segment_pattern' => '[0-9]+', //default
           'segment_name' => 'id', //default
           'children' => [
           ],
       ],
       '' => function($match) {
       }
       'actions' => ['show' => 'get'],
       //和 controller 一致，列出所有 action
       'actions' => ['show'],
       'shallow_nesting' => true,
       // get /articles/basic  patch/put /article/delete
       //when restful actoin convension is null, default => create => methods: all
       //if no restriction, all method except convensions is mapping to all
       //'extra_actions' => [], // extend default actions, if list one, list all - 一致性
       'actions' => [
           'setting' => ['methods' => ['get', 'post'], 'matcher' => function() {
           }],
       ],
       'callback' => function($ctx) {
           if ($ctx->match(':action')) {
               //check action
               return;
           } else {
               //fail
           }
       },
       'enable_show_and_index_action_convension' => false, // - cancel, 通过全局配置 - 一致性
       'enable_new_and_edit_action_convension' => false,//remove new and edit default action//use removeDefaultMethods method instead
       'enable_restful_action_convension' => false, //disable create update delete - cancel
       'formats' => ['default' => 'xml', 'rss'],
       'extra' => function($ctx) {
            if ($this->match('setting(/:xxxx)');
            $ctx->addAction();
            if ($ctx->match('get', 'edit')) {
                $ctx->setAction('edit');
                return true;
            }
            if (in_array($ctx->getAction(), ['edit', 'create'])) {
                $method = $this->getMethod();
                $this->setController('articles_comments');
                $this->setControllerClass('ArticlesCommentsController');
                $this->setActionMehtod('doNewAction');
                $this->setAction('xxx');
                $this->setModule();
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
