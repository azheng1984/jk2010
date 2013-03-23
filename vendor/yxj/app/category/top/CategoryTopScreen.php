<?php
class CategoryTopScreen extends Screen {
  private $category;

  public function __construct() {
    if (!is_array($GLOBALS['PATH_SECTION_LIST'][1])) {
      throw new NotFoundException;
    }
    $id = $GLOBALS['PATH_SECTION_LIST'][1][1];
    $this->category = Db::getRow('SELECT * FROM category WHERE id = ?', $id);
    if ($this->category === false) {
      throw new NotFoundException;
    }
  }

  protected function renderHtmlHeadContent() {
    echo '<title>',$this->category['name'],'品牌排名/',$this->category['name'],'十大品牌 - 优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<div><a href="/">首页</a> › <a href="..">',$this->category['name'],'</a> › 品牌排名</div>';
    echo '<h1>',$this->category['name'],'品牌排名</h1>';
    echo '<div>2013十大',$this->category['name'],'品牌排名投票正在进行，为你喜欢的',$this->category['name'],'牌子投上一票吧。</div>';
    echo '<h2>2013',$this->category['name'],'十大品牌排行榜</h2>';
    echo '<ol>';
    echo '<li>1.</li>';
    echo '</ol>';
    echo '<div><a rel="nofollow" href="../top-2013/2">排名大于十的',$this->category['name'],'品牌</a></div>';
    echo '<hr />';
    echo '<h2>相关十大品牌排名投票</h2>';
    echo '<div>十大aaa品牌排名 | 十大bbb品牌排名 | 十大ccc品牌排名</div>';
    echo '<div>品牌排名由用户投票产生，仅供参考。返回 <a href="..">',$this->category['name'],'品牌列表</a>。</div>';
  }
}