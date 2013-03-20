<?php
class PaginationScreen {
  public static function render(
    $page, $total, $prefix = '', $postfix = '', $pageOnePath = '.',
    $maximumPage = 100, $itemsPerPage = 50, $rel = ' rel="nofollow"'
  ) {
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
      $path = $previousPage === 1 ?
        $pageOnePath : $prefix.$previousPage.$postfix;
      echo '<a class="prev" href="', $path, '"', $rel, '>‹ 上一页</a> ';
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
        echo ' <span'.$class.'>', $index, '</span>';
        continue;
      }
      $path = $index === 1 ? $pageOnePath : $prefix.$index.$postfix;
      echo ' <a href="', $path, '"', $rel, $class , '>',
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