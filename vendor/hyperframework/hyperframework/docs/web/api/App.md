<?php
#App Class
return [
    'namespace' => 'Hyperframework\Web',
    'parent' => 'Hyperframework\Common\App',
    'methods' => [
        [
            'name' => '__construct',
            'visibility' => 'public',
            'static' => true,
        ], [
            'name' => '__construct',
            'visibility' => 'public',
            'static' => true,
        ],
    ]
];
/*
<span style="color: #936;">public</span> <span style="color: #936;">static</span> <span style="color: #693;">void</span> [run](/cn/docs/web/api/Web.run)()

public [__construct](/cn/docs/web/api/Web.__construct)(string $appRootPath)

public Router [getRouter](/cn/docs/web/api/Web.getRouter)()

public void quit() 继承

public void initializeConfig() 继承

protected static App createApp()

protected void rewriteRequestMethod()

protected void checkCsrf()

protected Controller createController()

protected void initializeErrorHandler(string $defaultClass = null)

protected void finalize() 继承
*/