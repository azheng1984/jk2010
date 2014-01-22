<?php
namespace Yxj\DataBinder;

//validation / upload filter
//save to db
//may be rebind to form
//在 rebind 到 form 时，只需要设置 form 的 data
//如果一个页面上有多个同类型的 form 需要 rebind，就 data 设置到 binding 到 collection 中, 再分发
class ArticleDataBinder {
    protected function getInstance() {
        return 'instance';
    }

    protected function getInputConfig() {
        return array(
            'user_name' => array(
                'max_length' => 10,
                'min_length' => 6,
                'is_nullable' => false,
                'type' => 'alpha & number'
            )
        );
       // if ($result['is_success']) {
       //     Web\Application::redirect('/article/' . $result['id'], 302);
       // }
    }

    protected static function renderForm() {
    }

    public function bind() {
        Form::render(
            'textbox'
        );

        $form = new ArticleForm($data);

        Html::textbox();

        $html = Html::bind('article', $data);


        $form = new Form($data);
        $form->begin(array('name' => 'article', 'method' => 'post'));
        $form->textbox(array('name' => 'title'));
        $form->select(array('name' => 'sex'));
        $form->end();

        Html::addDataSource('xyj/article', $data);
        Html::beginForm(array('name' => 'article', 'method' => 'post'));
        Html::textbox('bind' => 'xyj/article:title');
        Html::textArea('bind' => 'xyj/article:content');
        Html::endForm();

        $html->textarea(array('name' => 'article'));

        $form = new Form($data);
        $form->textbox(array('name' => '@title'));
        $form->textarea();

        Html::bindValue($data, 'title');
        Html::textbox('data_source' => $data['title'], '');

        echo '<input type="text" value=""/>';

        Html::textbox('data' => $data'title', 'title');
        $form->begin();
        $form->textbox('title');
        $form->textarea('content');
        $form->end();

        Html::textbox('data' => $data, 'title' => '');

        ArticleForm::begin('article');
        ArticleForm::textbox();
        ArticleForm::end();

        if (isset($data['id'])) {
            //insert
            return;
        }
        //update
    }
}
