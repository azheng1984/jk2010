<?php
class ProductImageUpdater {
  public function execute($item) {
    $product = DbProduct::get($item['product_id']);
    $image = DbImage::get($item['product_id']);
    $hasWebImage = DbProduct::hasWebImage($product['web_product_id']);
    $this->updateImage($product['web_product_id'], $hasWebImage, $image);
    if ($hasWebImage !== ($image !== false)) {
      DbProduct::updateHasWebImage(
        $product['web_product_id'], $image !== false
      );
    }
    exit;
  }

  private function updateImage($webProductId, $hasWebImage, $image) {
    if ($image === false) {
      $this->delateImage($webProductId, $hasWebImage);
      return;
    }
    if ($hasWebImage) {
      DbImage::updateWebImage($webProductId, $image);
      return;
    }
    DbImage::insertWebImage($webProductId, $image);
    return;
  }

  private function deleteImage($webProductId, $hasWebImage) {
    if ($hasWebImage) {
      DbImage::deleteWebImage($webProductId);
    }
  }
}