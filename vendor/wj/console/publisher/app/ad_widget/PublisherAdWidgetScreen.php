<?php
class PublisherAdWidgetScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box">首页，搜索结果页 - 交互式文档（url 约定结构），图标/图片 资源库</div>';
  }

  protected function getTitle() {
    return '广告发布商/广告控件';
  }
}