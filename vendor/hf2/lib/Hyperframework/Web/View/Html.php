<?php
namespace Hyperframework\Web\View;

abstract class Html {
    private $jsList = array();
    private $cssList = array();
    private $js = '';
    private $stop = false;

    abstract protected function renderHtmlHeadContent();
    abstract protected function renderHtmlBodyContent();

    public static function renderJsLink($path, $options = null) {
        if (Config::get('Hyperframework\Html\MergeJsEnabled')) {
            Html::renderJsLink('app');
            return;
        }
        Html::renderJsLink('common');
        Html::renderJsLink('product');
    }

    private function renderJsLinks() {
        if (($preloadJsUrls = JsPreloader::getUrls()) === null) {
            $this->renderPreloadedJsLinks();
            return;
        }
        foreach ($preloadJsUrls as $url) {
            JsLink::render($url); 
        }
    }

    protected function renderPreloadedJsLinks() {}

    public static function renderCssLink($path, $options = null) {
    }

    //    public function addJs($js) {
    //        $this->js .= $js;
    //    }

    //    protected function addCssLink($name) {
    //        $this->cssList[] = $name;
    //    }
    //
    //    protected function addJsLink($name) {
    //        $this->jsList[] = $name;
    //    }

    protected function stop() {
        $stop = true;
    }

    protected function renderContent() {
        header('Cache-Control: private, max-age=0');
        if ($this->stop === false) {
            echo '<!DOCTYPE html><html>';
            $this->renderHtmlHead();
            $this->renderHtmlBody();
            echo '</html>';
        }
    }

    private function renderCssLinkList() {
        foreach ($this->cssList as $name) {
            echo '<link type="text/css" href="/asset/css/', $name, '.',
                Asset::getMd5('css/'.$name.'.css'), '.css"',
                ' media="screen" rel="stylesheet"/>';
        }
    }

    private function renderJsLinkList() {
        foreach ($this->jsList as $name) {
            echo '<script type="text/javascript" src="/asset/js/', $name, '.',
                Asset::getMd5('js/'.$name.'.js'), '.js"></script>';
        }
    }

    private function renderHtmlHead() {
        echo '<head><meta charset="UTF-8"/>';
        $this->addCssLink('screen');
        $this->addJsLink('jquery-1.9.1');
        $this->addJsLink('screen');
        $this->renderHtmlHeadContent();
        $this->renderCssLinkList();
        echo '</head>';
    }

    private function renderHtmlBody() {
        echo '<body>';
        $this->renderBodyWrapper();
        $this->renderJsLinkList();
        $this->renderJs();
        echo '</body>';
    }

    private function renderBodyWrapper() {
        $this->renderBodyHeader();
        echo '<div class="content_wrapper">';
        $this->renderHtmlBodyContent();
        echo '</div>';
        $this->renderBodyFooter();
    }

    private function renderBodyHeader() {
        echo '<div id="top_wrapper"><div id="top">',
            '<div id="toolbar">';
        //$this->renderToolbar();
        echo '</div></div></div>';
        echo '<div id="header_wrapper"><div id="header">',
            '<div id="logo"><a href="/">优选集</a> - 品牌消费导航</div>';
        $this->renderSearch();
        echo '</div></div>';
    }

    private function renderSearch() {
        $query = isset($GLOBALS['QUERY']) ?
            htmlentities($GLOBALS['QUERY']['name'], ENT_QUOTES, 'UTF-8') : '';
        echo '<form action="/"><input type="text" name="q" maxlength="100" value="',
            $query, '"/> <button type="submit"></button></form>';
    }

    private function renderToolbar() {
        if (isset($_SESSION['user_id'])) {
            $user = Db::getRow(
                'SELECT * FROM user WHERE id = ?', $_SESSION['user_id']
            );
            $GLOBALS['USER'] = $user;
            echo '<a href="/user-',$user['id'],'">'.$user['name'].'</a>',
                ' | <a href="/draft">草稿</a>(', $user['draft_amount'], ')',
                '</a> | <a href="/setting">设置</a> | <a href="/sign_out">退出</a>';
            return;
        }
        echo '<a href="/sign_in">登录</a> <a href="/sign_up">注册</a>';
    }

    private function renderBodyFooter() {
        echo '<div id="footer_wrapper"><div id="footer">© 2013 优选集',
            ' <a href="/about/">关于优选集</a>',
            ' <a href="/about/copyright" rel="nofollow">版权声明</a>',
            ' <a href="/about/terms_of_use" rel="nofollow">使用条款</a>',
            ' <a href="/about/privacy" rel="nofollow">隐私权政策</a>',
            ' <a id="icp" href="http://www.miibeian.gov.cn/" target="_blank">',
            '沪ICP备xxxxxxxxxx号</a>';
        '</div></div>';
    }

    private function renderJs() {
        if ($this->js !== '') {
            echo '<script type="text/javascript">', $this->js, '</script>';
        }
    }
}
