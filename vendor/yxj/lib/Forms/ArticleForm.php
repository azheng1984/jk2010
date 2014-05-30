<?php
namespace Yxj\Forms;

class ArticleFrom extends Hyperframework\Web\Form {
    public function render($data) {
        FormBuilder::render(array(
            'attr' => 'id="article" name="article"'
                . ' method="POST" action="/article/new"',
            'field_config_name' => 'article'
        ));
    }

    public function post() {
        $articleFilter = new FormFilter('article');
        if ($articleFilter->isValid()) {
            $article = $articleFilter->getData();
            //other validation logic
        }

        return array(
            'id' => array(
                'tag' => 'Hidden',
                'attr' => 'class="hello"',
                'rules' => array(
                    'type' => 'int',
                    'max' => 100000,
                    'required',
                ),
            ),
            'category' => array(
                'label' => '分类',
            ),
            'submit' => array(
                'id',
                'attr' => 'value="提交"'
            )
        );
    }

    public function isValid() {
        parent::isValid();
        $articleFormFilter = new FormFilter('article');
        if ($articleFormFilter->isValid()) {
            
        }
        if (ArticleForm::isInputDataValid()) {
            $article = ArticleForm::getInputData();
        }
    }

    public function getInputValue {
    }
}
