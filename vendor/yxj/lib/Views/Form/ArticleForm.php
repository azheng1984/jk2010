<?php
class ArticleForm {
    public static function render() {
        $data = Applicaton::getActionResult();

        $title = Applicaton::getActionResult('article', 'title');

        $result['article']['title'];
//        Applicaton::getActionResult('data', 'basic');

        Html::beginForm('data_source' => Application::getActionResult('data'));
    }
}
