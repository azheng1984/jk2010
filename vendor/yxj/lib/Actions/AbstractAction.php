<?php
namespace Yxj\Action;

abstract class AbstractAction {
    public function before($ctx) {
        $ctx->get('id');
        //$aticle = ArticleDb::findById();
        static $context = \Hyperframework\Web\Application::getActionResult('article');
        return {'message' => 'hi'};
        //check autentication
    }
}
