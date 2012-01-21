<?php
class PaginationScreen {
  public static function render(
    $postfix = '', $total, $itemsPerPage = 16, $rel = ' rel="nofollow"'
  ) {
    if ($total <= $itemsPerPage) {
      return;
    }
    $currentPage = 1;
    if (isset($GLOBALS['URI']['PAGE'])) {
      $currentPage = intval($GLOBALS['URI']['PAGE']);
    }
    $totalPage = self::getTotalPage($total, $itemsPerPage);
    $firstPage = self::getFirstPage($currentPage, $totalPage);
    echo '<div id="page">';
    if ($currentPage !== 1) {
      $previousPage = $currentPage - 1;
      $path = $previousPage === 1 ? '.' : $previousPage;
      echo '<a href="', $path, $postfix, '"', $rel, '>&laquo; 上一页</a>';
    }
    $lastPage = $firstPage + 9;
    if ($lastPage > $totalPage) {
      $lastPage = $totalPage;
    }
    for ($index = $firstPage; $index <= $lastPage; ++$index) {
      if ($index === $currentPage) {
        echo ' <span>', $index, '</span>';
        continue;
      }
      $path = $index === 1 ? '.' : $index;
      echo ' <a href="', $path, $postfix, '"', $rel, '>',
        $index, '</a>';
    }
    if ($currentPage !== $totalPage) {
      echo ' <a href="', ($currentPage + 1),
        $postfix, '"', $rel, '>下一页 &raquo;</a>';
    }
    echo '</div>';
  }

  private static function getTotalPage($total, $itemsPerPage) {
    $remainder = $total % $itemsPerPage;
    if ($remainder === 0) {
      return $total / $itemsPerPage;
    }
    return ($total + $itemsPerPage - $remainder) / $itemsPerPage;
  }

  private static function getFirstPage($currentPage, $totalPage) {
    $result = 1;
    if ($currentPage > 5) {
      $result = $currentPage - 4;
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