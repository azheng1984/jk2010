<?php
namespace Yxj\Action;

use Hyperframework\Web\Application;
use Hyperframework\Web\InputMapper;

abstract class ArticleAction {
    public function before() {
        Security::check();//or bind user
    }

    protected function save() {
        $mapper = new InputMapper(array(
            'user_name' => array(
                'max_length' => 10,
                'min_length' => 6,
                'is_nullable' => false,
                'rename' => 'name',
                'type' => 'alpha'
            ),
            'avatar' => array(
                'type' => 'file',
                'target_path' => 'xxx'
            )
        ));
        $mapper = new InputMapper(array(
            'data_source' => 'get',
            'name' => 'query',
            'max_length' => 100
        ));


        $data = $mapper->getData();
        $errors = $mapper->getErrors();
        if ($mapper->isValid()) {
            //upload
            //$mapper->isValid();
        } else {
        }

        try {
            $data = InputMapper::map($config);
            //save...
            //redirect
        } catch (ValidationException $exception) {
            return ['errors' => $exception->getErrors()];
        }

        Html::bind($data, $errors);

        $errors = $mapper->getErrors();
        if ($mapper->isValid()) {
            if (isset($data['id'])) {
                $userId = DbArticle::getUserIdById($data['id']);
                if ($userId === $this->user['id']) {
                    DbArticle::updateDifference($data, $article);
                } else {
                    //http 401 
                }
            } else {
                $data['user_id'] = $this->userId;
                $data['id'] = DbArticle::insert($data);
            }
            Application::redirect('/article/' . $data['id'], 302);
            return;
        }
        return array('article' => $data, 'errors' => $errors);









        $article = Application::get('article');

        Validator::isValidRow();
        Validator::isValid();



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
