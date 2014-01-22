<?php
namespace Yxj\Action;

use Hyperframework\Web;
use Yxj\DataBinder\ArticleDataBinder;

abstract class ArticleAction {
    public function before() {
        //check autentication
    }

    protected function bind() {
        $bindingResult = DataBinder::bind(
            array(
                'id' => array(
                    'type' => 'auto_increment',
                ),
                'user_name' => array(
                    'max_length' => 10,
                    'min_length' => 6,
                    'is_nullable' => false,
                    'type' => 'alpha & number'
                ),
            ),
            'Yxj\Db\DbArticle',
            //'Yxj\Form\ArticleForm'
        );
        if ($bindingResult['status'] === 'success') {
            $id = DbArticle::bind($bindingResult['data']);
            Yxj\Form\ArticleForm::binding();
        }
        $data = $bindResult['data'];
        if ()
        //ArticleForm::instance($data)
        $this->data = $data;
        $this->errors = $errors;

        if (ArticleForm::bind()) {

            Web\Application::redirect(
                '/article/' . ArticleForm::select('id'), 302
            );
        }

        //ArticleForm::render();
        ArticleForm::render();

        $result = new DataBinder::bind(
            array(
                'user_name' => array(
                    'max_length' => 10,
                    'min_length' => 6,
                    'is_nullable' => false,
                    'type' => 'alpha & number'
                )
            ),
            'Yxj\Db\DbArticle',
            'Yxj\View\ArticleForm'
        );
        ArticleForm::render();

        //ArticleForm::bind
        $this->form = Html::createForm($result['data']);
        $this->errors = $result['errors'];

        //ArticleForm::render
        $form->renderTextbox('user_name');
        $form->renderPassword('password');

        if ($dataBinder->binder()) {
            Web\Application::redirect('/article/' . $dataBinder->get['id'], 302);
        }
        ArticleDataBinder::bind();
        $article = ArticleDataBinder::getData('*');

        if (ArticleDataBinder::isSuccess()) {
            Web\Application::redirect('/article/' . $result['id'], 302);
        }
        $result = Web\DataBinder::bind(
            array(
                'user_name' => array(
                    'max_length' => 10,
                    'min_length' => 6,
                    'is_nullable' => false,
                    'type' => 'alpha & number'
                )
            ),
            'Yxj\Db\DbArticle',
            true
        );
        if ($result['is_success']) {
            Yxj\Db\DbArticle::bind($);
            Web\Application::redirect('/article/' . $result['id'], 302);
        }
    }
}
