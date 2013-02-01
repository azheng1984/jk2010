<?php
class HomeScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 攻略聚集地</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="home" class="content">';
    echo '<div id="slogan">Slogan：Who am I <a href="/article-1/">了解更多</a></div>';
    echo '<div id="category_list">';
    echo '<p><a href="/category-1/">装饰装修</a></p>';
    echo '<p><a href="/category-1/">美食</a></p>';
    echo '<p><a href="/category-3/">家电</a></p>';
    echo '<p><a href="/category-4/">数码</a></p>';
    echo '<p><a href="/category-6/">摄影摄像</a></p>';
    echo '<p><a href="/category-5/">美容护理</a></p>';
    echo '<p><a href="/category-5/">运动户外</a></p>';
    echo '<p><a href="/category-5/">游戏</a></p>';
    echo '<p><a href="/category-2/">旅行</a></p>';
    echo '<p><a href="/category-2/">理财</a></p>';
    echo '<p><a href="/category-6/">结婚</a></p>';
    echo '<p><a href="/category-6/">母婴</a></p>';
    echo '<p><a href="/category-6/">学习</a></p>';
    echo '<p><a href="/category-6/">工作</a></p>';
    echo '<p><a href="/category-2/">房产</a></p>';
    echo '<p><a href="/category-2/">汽车</a></p>';
    echo '<p><a href="/category-1/">宠物</a></p>';
    echo '<p><a href="/category-1/">音乐</a></p>';
    echo '<p><a href="/category-1/">棋牌</a></p>';
    echo '<p><a href="/category-1/">书画</a></p>';
    echo '<p><a href="/category-1/">园艺</a></p>';
    echo '<p><a href="/category-1/">艺术品收藏</a></p>';
    echo '<p><a href="/category-1/">医疗</a></p>';
    echo '<p><a href="/category-1/">居家</a></p>';
    echo '<p><a href="/category-1/">养生保健</a></p>';
    echo '<p><a href="/category-1/">法律</a></p>';
    echo '<p><a href="/category-1/">本地生活</a></p>';
    echo '<p><a href="/category-0/">其他</a></p>';
    echo '</div>';
    echo '</div>';
  }
}