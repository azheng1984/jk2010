delete from wj_builder.key_mva_index where category_id != 0;
delete from wj_builder.`spider_product-web_product` where id != 0;
delete from wj_search.product where id != 0;
delete from wj_web.category where id != 0;
delete from wj_web.product where id != 0;
delete from wj_web.property_key where id != 0;
delete from wj_web.property_value where id != 0;

delete from wj_web.query where id != 0;
delete from wj_search.query where id != 0;

delete from `jingdong`.`category` where id != 0;
delete from `jingdong`.`food_product` where id != 0;
delete from `jingdong`.`food_product-property` where id != 0;
delete from `jingdong`.`food_product_log` where id != 0;
delete from `jingdong`.`food_property_key` where id != 0;
delete from `jingdong`.`food_property_value` where id != 0;

delete from `jingdong`.`task` where id != 0;
delete from `jingdong`.`task_record` where id != 0;
delete from `jingdong`.`task_retry` where task_id != 0;