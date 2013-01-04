<?php
class NavigationScreen {
  public static function render() {
    echo '<ul>';
      if ($GLOBALS['NAVIGATION_MODULE'] === 'book') {
      echo '<li>浏览</li>';
    } else {
      echo '<li><a href="/book/1/">浏览</a></li>';
    }
    if ($GLOBALS['NAVIGATION_MODULE'] === 'discussion') {
      echo '<li>讨论</li>';
    } else {
      echo '<li><a href="/book/1/discussion/">讨论</a></li>';
    }
    if ($GLOBALS['NAVIGATION_MODULE'] === 'task') {
      echo '<li>任务</li>';
    } else {
      echo '<li><a href="/book/1/task/">任务</a></li>';
    }
    if ($GLOBALS['NAVIGATION_MODULE'] === 'history') {
      echo '<li>历史</li>';
    } else {
    echo '<li><a href="/book/1/history/">历史</a></li>';
    }
    if ($GLOBALS['NAVIGATION_MODULE'] === 'download') {
      echo '<li>下载</li>';
    } else {
      echo '<li><a href="/book/1/download/">下载</a></li>';
    }
    if ($GLOBALS['NAVIGATION_MODULE'] === 'member') {
      echo '<li>成员</li>';
    } else {
      echo '<li><a href="/book/1/member/">成员</a></li>';
    }
    echo '</ul>';
  }
}