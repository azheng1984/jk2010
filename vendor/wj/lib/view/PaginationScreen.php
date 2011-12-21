<?php
class PaginationScreen {
  public static function render(
    $total, $itemsPerPage = 16, $prefix = '', $rel = ' rel="nofollow"'
  ) {
    if ($total <= $itemsPerPage) {
      return;
    }
    $currentPage = 1;
    if (isset($GLOBALS['URI']['PAGE'])) {
      $currentPage = $GLOBALS['URI']['PAGE'];
    }
    $totalPage = self::getTotalPage($total, $itemsPerPage);
    $firstPage = self::getFirstPage($currentPage, $totalPage);
    $postfix = '';
    if ($_SERVER['QUERY_STRING'] !== '') {
      $postfix = '?'.$_SERVER['QUERY_STRING'];
    }
    $result = '<div id="pagination">';
    if ($currentPage != 1) {
      $previousPage = $currentPage - 1;
      $path = $previousPage === 1 ? '.' : $previousPage;
      $result .= '<a'.$rel.' href="'
        .$prefix.$path.$postfix. '">&laquo; 上一页</a>';
    }
    $lastPage = $firstPage + 9;
    if ($lastPage > $totalPage) {
      $lastPage = $totalPage;
    }
    for ($index = $firstPage; $index <= $lastPage; ++$index) {
      if ($index == $currentPage) {
        $result .= ' <span>' . $index . '</span>';
        continue;
      }
      $path = $index === 1 ? '.' : $index;
      $result .= ' <a'.$rel.' href="'.$prefix.$path.$postfix.'">'.$index.'</a>';
    }
    if ($currentPage != $totalPage) {
      $result .= ' <a'.$rel.' href="'
        .$prefix.($currentPage + 1).$postfix.'">下一页 &raquo;</a>';
    }
    echo $result, '</div>';
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