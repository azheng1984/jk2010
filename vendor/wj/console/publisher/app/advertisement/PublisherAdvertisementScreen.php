<?php
class PublisherAdvertisementScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><div class="title">购物助手</div>应用整合/浏览器插件</div>';
    echo '<div class="box"><div class="title">自定义链接</div>商标和内容显示政策/url 约定结构</div>';
  }

  protected function getTitle() {
    return '广告发布商/广告控件';
  }
}