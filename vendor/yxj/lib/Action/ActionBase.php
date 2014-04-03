<?php
namespace Yxj\Action;

abstract class BaseAction {
    public function before() {
        //$aticle = ArticleDb::findById();
        static $context = \Hyperframework\Web\Application::getActionResult('article');
        return {'message' => 'hi'};
        //check autentication
    }
}
