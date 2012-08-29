<?php
class PublisherAdvertisementScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><div class="title">网页插件</div>使用文档/sdk</div>';
    echo '<div class="box"><div class="title">浏览器插件</div>使用文档 & 定制插件生成器</div>';
    echo '<div class="box"><div class="title">自定义链接</div>icon（桌面链接）/logo 和内容显示政策/url 约定结构/widget 生成器</div>';
  }

  protected function getTitle() {
    return '广告发布商/广告控件';
  }
}