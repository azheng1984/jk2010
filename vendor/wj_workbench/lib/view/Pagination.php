<?php
class Pagination {
  function render($prefix, $total, $currentPage, $specialForPageOne = null) {
    define('ITEM_PER_PAGE', 20);
    //把总数补足成 ITEM_PER_PAGE 的倍数
    if ($total % ITEM_PER_PAGE != 0) {
      $total += ITEM_PER_PAGE - $total % ITEM_PER_PAGE;
    }
    
    $totalPage = $total / ITEM_PER_PAGE;
    
    //保证有一页存在
    if ($totalPage == 0) {
      $totalPage = 1;
    }
    
    //为隐藏分页作准备
    $totalPageItem = 11; //最多显示的页面条目数
    $startPageItem = $currentPage - $currentPage % 10 + 1;
    
    if ($currentPage % 10 == 0) {
      $startPageItem -= 10;
    }

    $result = '';
    //Previous
    if ($currentPage != 1) {
      if ($specialForPageOne != null && $currentPage == 2) {
        $result .= '<a href="' . $specialForPageOne . '">&laquo; 上一页</a>';
      } else {
        $prevPage = $currentPage - 1;
        $result .= '<a href="' . $prefix;
        if ($prevPage != 1) {
          $result .= '-' . $prevPage;
        }
        $result .= '.html">&laquo; 上一页</a>';
      }
    }

    if ($startPageItem != 1) {
      $result .= ' &hellip; ';
    }

    $count = $startPageItem;
    for (; $count <= $totalPage && $totalPageItem > 0; ++$count) {
      if ($count == $currentPage) {
        $result .= '<span>' . $count . '</span>';
      } else {
        if ($specialForPageOne != null && $count == 1) {
          $result .= ' <a href="' . $specialForPageOne . '">1</a>';
        } else {
          $result .= ' <a href="' . $prefix;
          if ($count != 1) {
            $result .= '-' . $count;
          }
          $result .= '.html">' . $count . '</a>';
        }
      }
      --$totalPageItem;
    }
    if ($count != $totalPage + 1) {
      $result .= ' &hellip; ';
    }

    //Next
    if ($currentPage != $totalPage && $totalPage != 0) {
      $result .= ' <a href="' . $prefix . '-' . ($currentPage + 1) . '.html">下一页 &raquo;</a>';
    }
    echo $result;
  }
}