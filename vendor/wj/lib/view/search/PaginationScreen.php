<?php
class PaginationScreen {
  public static function render($prefix, $total, $specialForPageOne = null) {
    if ($total <= 20) {
      return;
    }
    $currentPage = 1;
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
      $currentPage = $_GET['page'];
    }
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
    $totalPageItem = 10; //最多显示的页面条目数
    $startPageItem = 1;
    if ($currentPage > 5) {
      $startPageItem = $currentPage - 4;
      if (($currentPage + 5) > $totalPage) {
        $startPageItem = $startPageItem - (5 - ($totalPage - $currentPage));
      }
    }

    $result = '<div id="pagination"> ';
    //Previous
    if ($currentPage != 1) {
      if ($specialForPageOne !== null && $currentPage == 2) {
        $result .= '<a rel="nofollow" href="' . $specialForPageOne . '">&laquo; 上一页</a>';
      } else {
        $prevPage = $currentPage - 1;
        $result .= '<a rel="nofollow" href="' . $prefix;
        if ($prevPage != 1) {
          $result .= 'page=' . $prevPage;
        }
        $result .= '">&laquo; 上一页</a>';
      }
    }

    if ($startPageItem != 1) {
      $result .= ' &hellip; ';
    }

    $count = $startPageItem;
    for (; $count <= $totalPage && $totalPageItem > 0; ++$count) {
      if ($count == $currentPage) {
        $result .= ' <span>' . $count . '</span>';
      } else {
        if ($specialForPageOne !== null && $count == 1) {
          $result .= ' <a rel="nofollow" href="' . $specialForPageOne . '">1</a>';
        } else {
          $result .= ' <a rel="nofollow" href="' . $prefix;
          if ($count != 1) {
            $result .= 'page=' . $count;
          }
          $result .= '">' . $count . '</a>';
        }
      }
      --$totalPageItem;
    }
    if ($count != $totalPage + 1) {
      $result .= ' &hellip; ';
    }
    //Next
    if ($currentPage != $totalPage && $totalPage != 0) {
      $result .= ' <a rel="nofollow" href="' . $prefix . 'page=' . ($currentPage + 1) . '">下一页 &raquo;</a>';
    }
    echo $result, '</div>';
  }
}