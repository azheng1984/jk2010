<?php
class PaginationScreen {
  public static function render($page, $total, $prefix, $postfix,
    $maximumPage = 50, $itemsPerPage = 20, $rel = ' rel="nofollow"') {
    if ($total <= $itemsPerPage) {
      return;
    }
    $totalPage = (int)ceil($total / $itemsPerPage);
    if ($totalPage > $maximumPage) {
      $totalPage = $maximumPage;
    }
    $firstPage = self::getFirstPage($page, $totalPage);
    echo '<div id="pagination">';
    if ($page !== 1) {
      $previousPage = $page - 1;
      $path = $previousPage === 1 ? '.' : $previousPage;
      echo '<a class="prev" href="', $prefix, $path, $postfix, '"', $rel, '>‹ 上一页</a> ';
    }
    $lastPage = $firstPage + 9;
    if ($lastPage > $totalPage) {
      $lastPage = $totalPage;
    }
    for ($index = $firstPage; $index <= $lastPage; ++$index) {
      $class = '';
      if ($index === $firstPage) {
        $class = ' class="first" ';
      }
      if ($index === $lastPage) {
        $class = ' class="last" ';
      }
      if ($index === $page) {
        echo '<span'.$class.'>', $index, '</span>';
        continue;
      }
      $path = $index === 1 ? '.' : $index;
      echo '<a href="', $prefix, $path, $postfix, '"', $rel, $class , '>',
        $index, '</a>';
    }
    if ($page !== $totalPage) {
      echo ' <a class="next" href="', $prefix, ($page + 1),
        $postfix, '"', $rel, '>下一页 ›</a>';
    }
    echo '</div>';
  }

  private static function getFirstPage($page, $totalPage) {
    $result = 1;
    if ($page > 5) {
      $result = $page - 4;
    }
    if (($result + 9) > $totalPage) {
      $result = $totalPage - 9;
    }
    if ($result < 1) {
      $result = 1;
    }
    return $result;
  }
}