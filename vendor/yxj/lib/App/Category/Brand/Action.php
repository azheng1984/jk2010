<?php 
class CategoryBrandAction {
    public function PUT() {
        $categoryId = $GLOBALS['PATH_SECTION_LIST'][1][1];
        $brandId = $_GET['brand_id'];
        $pr = $_POST['pr'];
        Db::update(
            'brand_category',
            array('popularity_rank' => $pr),
            'category_id = ? AND brand_id = ?', $categoryId, $brandId
        );
    }
}
