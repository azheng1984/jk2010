<?php
class CategoryTopScreen extends Screen {
//  private $category;

  public function __construct() {
  }

  protected function renderHtmlHeadContent() {
    echo '<title>xxx品牌排名/xxx十大品牌 - 优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<div><a href="/">首页</a> › xxx › 品牌排名</div>';
    echo '<h1>xxx品牌排名</h1>';
    echo '<div>2013十大xxx品牌排名投票正在进行，为你喜欢的牌子投上一票吧。</div>';
    echo '<h2>2013xxx十大品牌</h2>';
    echo '<ol>';
    echo '<li>1.</li>';
    echo '</ol>';
    echo '<div><a rel="nofollow" href="../top-2013/2">排名大于十的xxx品牌</a></div>';
    echo '<div>品牌排名由用户投票产生，仅供参考。返回 <a href="#">xxx品牌列表</a>。</div>';
    echo '<hr />';
    echo '<h2>相关十大品牌排名投票</h2>';
    echo '<div>十大xxx品牌排名 | </div>';
  }
}