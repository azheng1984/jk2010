<?php
namespace Yxj\Action;

use Hyperframework\Web;

abstract class ArticleAction {
    public function before() {
        Security::check();//or bind user
    }

    protected function bind() {
        //和 validator 一起使用, validator 用于不许要提取的场景
        $mapper = new InputMapper(array(
            'user_name' => array(
                'max_length' => 10,
                'min_length' => 6,
                'is_nullable' => false,
                'type' => 'alpha & number'
            ),
            'avatar' => array(
                'type' => 'file',
                'target_path' => 'xxx'
            )
        ));
        Validator::isValidRow();
        Validator::isValid();
        $data = $mapper->getData();
        $errors = $mapper->getErrors();
        if ($mapper->isValid()) {
            if (isset($data['id'])) {
                $userId = DbArticle::getUserIdById($filterResult['data']['id']);
                if ($userId === $this->userId) {
                    //DbArticle::update($result['data']);
                    DbArticle::updateDifference($filterResult['data'], $article);
                } else {
                    //http 401 
                }
            } else {
                $filterResult['data']['user_id'] = $this->userId;
                $filterResult['data']['id'] = DbArticle::insert($result['data']);
            }
            Web\Application::redirect('/article/' . $result['data']['id'], 302);
            return;
        }
        return array('article' => $data, 'errors' => $errors);


        ArticleForm::bind($result['data'], $result['errors']);


        $result = InputFilter::getFile(array(
            'target_path' => 'xxx',
            'max_size' => '123k',
            'should_return' => true
        ));
 
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
