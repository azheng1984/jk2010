<?php
class JingDongImporterCommand {
  public function execute() {
    $sql = 'SELECT * FROM product';
    foreach (Db::getAll($sql) as $product) {
      $html = gzuncompress($product['html']);
      echo $product['id'];
      preg_match(
        '{class="Ptable">(.*?)</table>}',
        $html,
        $matches
      );
      if (isset($matches[1])) {
        $specTable = iconv('GBK', 'utf-8', $matches[1]);
        $sections = explode('<tr><th class="tdTitle" colspan="2">', $specTable);
        foreach ($sections as $section) {
          if ($section === '') {
            continue;
          }
          preg_match(
            '{^(.*?)</th><tr>(.*)}',
            $section,
            $matches
          );
          $sectionName = $matches['1'];
          $sql = "SELECT id FROM property_key WHERE `key` = ?";
          $parentId = Db2::getColumn($sql, $sectionName);
          if ($parentId === -1) {
            $sql = "INSERT INTO property_key(`key`) VALUES(?)";
            Db2::execute($sql, $sectionName);
            $parentId = Db2::getLastInsertId();
            exit;
          }
          exit;
          preg_match_all(
            '{<tr><td class="tdTitle">(.*?)</td><td>(.*?)</td></tr>}',
            $matches['2'],
            $matches,
            PREG_SET_ORDER
          );
          $brand = null;
          $model = null;
          $color = null;
          foreach ($matches as $match) {
            $key = $match[1];
            $value = $match[2];
            if ($key === '品牌') {
              $brand = $value;
              break;
            }
            if ($key === '型号') {
              $model = $value;
              break;
            }
            if ($key === '颜色') {
              $color = $value;
              break;
            }
            //insert product value
          }
          //insert product reco
        }
      }
    }
  }
}