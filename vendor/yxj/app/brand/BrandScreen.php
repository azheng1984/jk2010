<?php
class BrandScreen {
  public function __construct() {
    $id = $GLOBALS['PATH_SECTION_LIST'][1][1];
    $brand = Db::getRow('SELECT * FROM brand WHERE id = ?', $id);
    print_r($brand);
  }

  public function render() {
    echo '<a href="edit">编辑</a>';
    echo '<h1>立顿</h1>';
    echo '<div>logo</div>';
    echo '<div>abstract</div>';
    echo '<div>浏览：2323</div>';
    echo '<div>品牌发源地：<a href="/location-1/">英国</a></div>';
    echo '<div>description</div>';
    echo '<div>纠错</div>';
    echo '<h2>立顿相关的品牌</h2>';
    echo '<h2>立顿相关的品牌分类</h2>';
    echo '<div>食品饮料 > 茶 > 奶茶</div>';
    echo '<h2>立顿相关的十大品牌排名投票</h2>';
  }
}