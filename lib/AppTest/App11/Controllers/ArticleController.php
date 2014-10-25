<?php

class ArticleController {
    public function __construct() {
    }

    public function get() {
        //$this->activeCollectionView('Index'); - default
        //$this->activeMemberView('Show', 'New', 'Edit', 'Preview');
        $this->setMemberViews('Show', 'New', 'Edit', 'Preview');
        $this->setCollectionView('Index');
        $route = [
            'articles' => [
                'member_view' => [
                    ['comments'
                    ]
                ],
                'member_view' => [
            ]
        ]

        $urls = [
            'x11articles' => ['controller' => 'X11Article'],
            'x11articles/search' => 'search',
            'articles',
            'articles/search',
            'articles/json',
            'articles/tags' => ['view' => 'IndexTags'],
            'articles/:id' => ['include' => ['comments' => 'shallow', 'photos']],
            'articles/:id/edit',
            'articles/:id/new',
            'articles/:id/tags' => ['view' => 'ItemTags'],
            'articles/:id/create',
            'tags',
            'member',
            'member/profile',
            'member/show',
            'member/edit',
        ];

        $subdomans = ['admin', 'my.admin'];

        $rules = [
            'Admin' => [
                'subdomain' => 'subdomain/subdomain',
                'path' => '/',
            ],
            'admin/comments' => [
                'is_subdomain' => true,
            ],
            'admin/articles' => function($rules) {
                $rules
                    ->addChildren(['name' => 'comments', 'shallow' => true], 'photos')
                    ->setType('collection')
                    ->addMemberPaths('search')
                    ->addPath('/')
            },

            'admin/comments' => [
                'name' => 'AdminComment' //default
            ],

            'photo' => [
            ],

            [
                'include' => [
                    'comments' => 'shallow',
                    'photos' => [
                        'collection_paths' => [''],
                        'member_paths' => [
                            'new',
                            'new/preview'
                        ],
                        'actions' => ['create'],
                    ]
                ],
                'type' => 'collection',
                'paths' => [':id', ':id/search'],
                'id_format' => function($segment) {
                },
                'collection_paths' => ['.'],
            ],
        ];

        $relations = [
            'articles' => 'comments'
        ]

        $controllerMapping = array(
            'articles/comments' => ['shallow', 'controller' => 'ArticleCommentController'],
            'articles/x11_comments' => ['controller' => 'ArticleCommentController']
        ),
    }

    public function delete() {
    }

    public function update() {
    }

    public function create() {
    }
}
